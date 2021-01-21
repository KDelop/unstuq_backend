<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\UserDevice\UserDeviceRepositoryInterface;

use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;
use App\Models\UserCredit;
use App\Models\UserDevice;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
class ApiController extends Controller
{
    private $userRepository;
    private $userDeviceRepository;

    public function __construct(UserRepositoryInterface $userRepository,
            UserDeviceRepositoryInterface $userDeviceRepository){
        $this->userRepository = $userRepository;
        $this->userDeviceRepository = $userDeviceRepository;
    }

        /**
         * @api {post} /register User Register
         * @apiName User Register
         * @apiGroup LoginRegister
         *
         * @apiParam {String} name   Mandatory User Name.
         * @apiParam {String} email  Mandatory User Email.
         * @apiParam {String} phone  Mandatory User Phone.
         * @apiParam {String} countryCode  Mandatory User Country code.
         *
         * @apiSuccessExample Success-Response:
         *     HTTP/1.1 200 OK
         *  {
         *       "status": true,
         *       "message": "Account created successfully."
         *       "user_token" : "temparary token for verification api"
         *  }
         *
         * @apiErrorExample Error-Response:
         *     HTTP/1.1 200 Not Found
         *     {
         *        "status": false,
         *        "message": "An account with this email or phone number already exists. Please login instead."
         *     }
         *
         *  @apiErrorExample Error-Response:
         *     HTTP/1.1 400 Not Found
         *     {
         *        "status": false,
         *        "message": "The email field is required., The name field is required., The phone field is required."
         *     }
         */
    public function register(Request $request)
    {
        $response = ['status' => false, 'message' => 'Something Went wrong while processing'];
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
            'name' => 'required',
            'phone' => 'required',
            'countryCode' => 'required',
        ]);

        if ($validator->fails()) {
            $response =  get_parsed_validation_error_response($validator);
            return response()->json($response, 400);
        }else{
            try{
                $data = $request->only('phone','email','name','countryCode');
                $user =  $this->userRepository->user_exists_check($data);

                if($user && $user->status != 2){
                    $response['message'] = "An account with this email or phone number already exists. Please login instead.";
                }else{
                    $access_code = strtoupper(get_random_strings(6));
                    //$access_code = "WFIWBN";
                    $realphone = str_replace("-", "",  $data['phone']);
                    $realphone = str_replace(" ", "", $realphone);

                    if($user && $user->status == 2){
                        $this->userRepository->update([
                            'name' => ucfirst($data['name']),
                            'email' => $data['email'],
                            'phone' =>  $realphone,
                            'access_code' => $access_code,
                            'country_code' => $data['countryCode'],
                            'status' => 0 //unverified status
                        ],$user->id);
                    }else{
                        $user = $this->userRepository->create([
                            'name' => ucfirst($data['name']),
                            'email' => $data['email'],
                            'phone' =>  $realphone,
                            'access_code' => $access_code,
                            'country_code' => $data['countryCode'],
                            'status' => 0 //unverified status
                        ]);
                    }

                    if($user){
                        UserCredit::create(['user_id'=>$user->id,'balance'=>10]);
                        $msg_data = [
                            'name' => explode(" ",$data["name"])[0],
                            'access_code' => $access_code,
                        ];
                        $message_name = 'register_message';
                        $msg = get_message_text($message_name, $msg_data);

                        //send sms
                        send_access_code($access_code, $data['phone']);

                        mailjet_send_mail([
                            'SendTo' => $data['email'],
                            'FullName' => $data['name'],
                            'TemplateID' => '1363721',
                            'MailSubject' => 'Welcome to UnstuQ',
                            'UserName' => explode(" ", $data['name'])[0],
                            'Code' => $access_code
                        ]);

                        $response = ['status' => true ,'message' => 'Account created successfully.' ];
                        $response['user_token'] = base64_encode($user->id."_".gmdate("Y-m-d h:i:s"));

                    }
                }
            } catch (\Exception $e) {
                $response = error_reponse_handler($e);
                return response()->json($response['response'],$response['status_code']);
            }
        }
        return response()->json($response,200);
    }

        /**
         * @api {post} /login  User Login
         * @apiName User Login
         * @apiGroup LoginRegister
         *
         * @apiParam {String} phone  User Phone.
         *
         * @apiSuccessExample Success-Response:
         *     HTTP/1.1 200 OK
         *  {
         *       "status": true,
         *       "message": "Access code send to your email/phone no.",
         *       "user_token" : "temparary token for verification api"
         *  }
         *
         * @apiErrorExample Error-Response:
         *     HTTP/1.1 400 Not Found
         *     {
         *        "status": false,
         *        "message": "no email or phone provided"
         *     }
         */
    public function login(Request $request)
    {
        $response = ['status' => false, 'message' => 'Something Went wrong while processing'];

        $data = $request->only('phone','email');
        try{
            if(isset($data['phone'])){
                $realphone = str_replace("-", "",  $data['phone']);
                $realphone = str_replace(" ", "", $realphone);
                $user =  $this->userRepository->findOneFromArray(['phone' => $data['phone']]);
             }else if(isset($data['email'])){
                 $user =  $this->userRepository->findOneFromArray([ 'email' => $data['email'] ]);

            }else{
                $response['message'] = 'Phone/email not provided';
                return response()->json($response,400);
            }

            if($user){
				//print_r($user);
				//die();
                $isCredit = UserCredit::where('user_id',$user->id)->first();
                if(!$isCredit){
                    UserCredit::create(['user_id'=>$user->id,'balance'=>10]);
                }
				$access_code = $user->access_code;
					
				if(!isset($user->access_code) || $user->access_code == null || $user->access_code == "")
				{
					$access_code = strtoupper(get_random_strings(6));
					$resp = $user->update(['access_code' => $access_code]);
				}
                
                //$access_code = "WFIWBN";
                
                $msg_data = [
                    'access_code' => $access_code,
                ];
                $message_name = 'login_access_code_message';
                $msg = get_message_text($message_name, $msg_data);

                mailjet_send_mail([
                    'SendTo' => $user->email,
                    'FullName' => $user->name,
                    'TemplateID' => '1363741',
                    'MailSubject' => 'Welcome back to UnstuQ',
                    'UserName' => explode(" ", $user->name)[0],
                    'Code' => $access_code
                ]);
                
                if(isset($data['phone'])){
                    //send sms
                    send_access_code($access_code, $data['phone']);
					$response = ['status' => true ,'message' => 'Access code send to your phone no.' ];
                }
				
				if(isset($data['email']) && isset($user->phone)){
                    //send sms
                    send_access_code($access_code, $user->phone);
					$response = ['status' => true ,'message' => 'Access code send to your phone no.' ];
                }
                $response['user_token'] = base64_encode($user->id."_".gmdate("Y-m-d h:i:s"));
            }else{
                $response['message'] = "An account with this email/phone was not found. Please check your email or click Register to create a new account.";
            }
        } catch (\Exception $e) {
            $response = error_reponse_handler($e);
            return response()->json($response['response'],$response['status_code']);
        }

      return response()->json($response);
    }
    
        /**
         * @api {post} /verify_access_code  User Verify
         * @apiName User Verify
         * @apiGroup LoginRegister
         *
         * @apiParam {String} user_token  temparary token.
         * @apiParam {String} code  access code.
         * @apiParam {String} device_uuid  device unique id.
         * @apiParam {String} device_type  andriod (1) or ios (2) device.
         * @apiParam {String} device_name  device name.
         *
         * @apiSuccessExample Success-Response:
         *     HTTP/1.1 200 OK
         *  {
         *       "status": true,
         *       "message": "Access code send to your email/phone no.",
         *       "token" : "Authorization token"
         *  }
         *
         *  @apiErrorExample Error-Response:
         *     HTTP/1.1 200 Not Found
         *  {
         *        "status": false,
         *        "message": "Invalid code"
         *   }
         *
         *  @apiErrorExample Error-Response:
         *     HTTP/1.1 400 Not Found
         *  {
         *        "status": false,
         *        "message": "The code field is required., The user token field is required.",
         *   }
         */
    
    public function verify_access_code(Request $request){
        $response = ['status' => false, 'message' => 'Something Went wrong while processing'];

        $validator = Validator::make($request->all(), [
            'code' => 'required',
            'user_token' => 'required',
            'device_uuid' => 'required',
            'device_type' => 'required|in:1,2',
            'device_name' => 'required'
        ]);

        if ($validator->fails()) {
            $response =  get_parsed_validation_error_response($validator);
            return response()->json($response, 400);
        }else{
            try {
                $data = $request->only('code','user_token','device_uuid','device_type','device_name');
                $token = explode("_",base64_decode($data['user_token']));

                $now = \Carbon\Carbon::parse(gmdate("Y-m-d h:i:s"));
                $sendTime = \Carbon\Carbon::parse($token[1]);

                $hourDiff = $now->diffInHours($sendTime);

                if($hourDiff  < 1){ //if less than one hour
                    $user =  $this->userRepository->find($token[0]);
                    if($user->access_code != ""){
                        if($user->access_code == $data['code']){
                            //update user status to active
                            $user->access_code = '';
                            $user->status = 1;
                            $user->last_verified_at = gmdate('Y-m-d H:i:s');
                            $user->save();
                            $userdata = $user->toArray();
                            //return token
                            $token = JWTAuth::fromUser($user);
                            $response = [
                                'status' => true,
                                'message' => 'verified',
                                'token' => $token,
                                'data' => $userdata
                            ];

                            //add device or update it
                            $device = $this->userDeviceRepository->check_device_exists([
                                'user_id' => $user->id,
                                'device_uuid' => $data['device_uuid']
                            ]);

                            if($device){
                                //update
                                $this->userDeviceRepository->update(['updated_at' => gmdate("Y-m-d H:i:s")],$device->id);

                            }else{
                                //create new
                                $this->userDeviceRepository->create([
                                    'user_id' => $user->id,
                                    'device_uuid' => $data['device_uuid'],
                                    'device_type' => $data['device_type'],
                                    'device_name' => $data['device_name'],
                                ]);
                            }

                        }else{
                            $response['message'] = 'Invalid code';
                        }
                    }else{
                        $response['message'] = 'Already Verified or Invalid code';
                    }
                }else{
                    $response['message'] = 'Code expired.Please try again';
                }
            } catch (\Exception $e) {
                $response = error_reponse_handler($e);
                return response()->json($response['response'],$response['status_code']);
            }
        }
        return response()->json($response,200);
    }


        /**
         * @api {post} /logout  User Logout
         * @apiName verify user
         * @apiGroup LoginRegister
         *
         * @apiHeader {String} Authorization='bearer bd970a05-0ec1-4412-8b28-657962f0f778'
         *
         * @apiSuccessExample Success-Response:
         *     HTTP/1.1 200 OK
         *  {
         *       "status": true,
         *       "message": "Logout successfully",
         *  }
         *
         *  @apiErrorExample Error-Response:
         *     HTTP/1.1 200 Not Found
         *     {
         *        "status": false,
         *        "message": "Invalid Token"
         *     }
         */
    public function logout( Request $request ) {

        $token = $request->header( 'Authorization' );

        try {
            JWTAuth::parseToken()->invalidate( $token );

            return response()->json( [
                'status'   => true,
                'message' => "Logout successfully"
            ] );
        } catch ( TokenExpiredException $exception ) {
            return response()->json( [
                'status'   => false,
                'message' => "Expired Token"

            ], 401 );
        } catch ( TokenInvalidException $exception ) {
            return response()->json( [
                'status'   => false,
                'message' => "Invalid Token"
            ], 401 );

        } catch ( JWTException $exception ) {
            return response()->json( [
                'status'   => false,
                'message' => "Missing Token"
            ], 500 );
        }
    }

    /**
     * @api {get} /get-countries  Get Countries
     * @apiName Get Countries
     * @apiGroup User
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *   {
     *     "status": true,
     *     "data": {
     *          "countries" => []
     *      }
     * }
     */
    public function get_countries(Request $request){

        $response = ['status' => false, 'message' => 'Something Went wrong while processing'];
       try{
            $countries = \DB::table('countries')->where('status',1)->orderByRaw("order_value, name")->get();
            if($countries){
                $response = [
                    'status' => true,
                    'data' => [
                        'countries' => $countries
                    ]
                ];
            }
        }catch(\Exception $e) {
            $response = error_reponse_handler($e);
            return response()->json($response['response'],$response['status_code']);
        }

        return response()->json($response,200);
    }

    /** Test push notification**/
    public function testPushNotification(Request $request)
    {
        $response = ['status' => false, 'message' => 'Something Went wrong while processing'];
        try{
            $user_id = $request->input('user_id');
            $user =  $this->userRepository->findBy('id',$user_id);
            if($user){
                $user_device = $this->userDeviceRepository->findBy('user_id',$user['id']);
                $type = 'pending';
                $name = $user['name'];
                $inviter_name = "test user";
                $msg_data = [
                  'invited_user_name' => $name,
                  'user_invited_by' => $inviter_name,
                ];
                $message_name = 'added_to_group_notification_message';
                $msg = get_message_text($message_name, $msg_data);
                $response = sendMessage($user_device['player_id'],$msg,$type);
                $response = [
                    'status' => true,
                    'data' => [
                        'response' => $response
                    ]
                ];
            }
            else{
                $response = [
                    'status' => true,
                    'data' => [
                        'response' => 'user_id is not valid'
                    ]
                ];
            }

          //  $response = sendPushNotification($testDeviceId, $msg);
           /* Log::info("testPushNotification : ".$response);

            $this->assertTrue($response);*/
        }catch(\Exception $e) {
            $response = error_reponse_handler($e);
            return response()->json($response['response'],$response['status_code']);
        }

        return response()->json($response,200);
    }

}
