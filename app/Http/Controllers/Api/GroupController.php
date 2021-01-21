<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\UserDevice\UserDeviceRepositoryInterface;
use App\Repositories\UserGroup\UserGroupRepositoryInterface;
use App\Repositories\UserGroupMember\UserGroupMemberRepositoryInterface;
use App\Repositories\SearchTransaction\SearchTransactionRepositoryInterface;
use App\Repositories\SearchTransactionGroup\SearchTransactionGroupRepositoryInterface;
use App\Repositories\SearchTransactionUser\SearchTransactionUserRepositoryInterface;
use App\Repositories\MatchMaker\MatchMakerRepositoryInterface;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Validator;

class GroupController extends Controller
{
    private $userRepository;
    private $userGroupRepository;
    private $userGroupMemberRepository;
    private $searchTransactionGroupRepository;
    private $searchTransactionRepository;
    private $searchTransactionUserRepository;
    private $matchMakerRepository;
    private $userDeviceRepository;

    public function __construct(UserRepositoryInterface $userRepository,
                UserGroupRepositoryInterface $userGroupRepository,
                UserGroupMemberRepositoryInterface $userGroupMemberRepository,
                                SearchTransactionRepositoryInterface $searchTransactionRepository,
                                SearchTransactionUserRepositoryInterface $searchTransactionUserRepository,
                                MatchMakerRepositoryInterface $matchMakerRepository,
                                SearchTransactionGroupRepositoryInterface $searchTransactionGroupRepository,
                                UserDeviceRepositoryInterface $userDeviceRepository){
        $this->userRepository = $userRepository;
        $this->userGroupRepository = $userGroupRepository;
        $this->userGroupMemberRepository = $userGroupMemberRepository;
        $this->searchTransactionGroupRepository = $searchTransactionGroupRepository;
        $this->matchMakerRepository = $matchMakerRepository;
        $this->searchTransactionRepository = $searchTransactionRepository;
        $this->searchTransactionUserRepository = $searchTransactionUserRepository;
        $this->userDeviceRepository = $userDeviceRepository;
        $this->middleware('jwt.auth');
    }

        /**
         * @api {post} /group/create  Create User Group
         * @apiName Create User Group
         * @apiGroup UserGroup
         *
         * @apiDescription Note : user body/form-data paramter option for this api otherwise file will not be uploaded.
         *
         * @apiParam {String} group_name  group name.
         * @apiParam {File} [group_icon]  group icon.
         *
         * @apiHeader {String} Authorization='bearer bd970a05-0ec1-4412-8b28-657962f0f778'
         *
         * @apiSuccessExample Success-Response:
         *     HTTP/1.1 200 OK
         *  {
         *       "status": true,
         *       "message": "Successfully created group"
         *  }
         *
         */
    public function create(Request $request){

        $response = ['status' => false, 'message' => 'Something Went wrong while processing'];
        $validator = Validator::make($request->all(), [
            'group_name' => 'required',
        ]);

        if ($validator->fails()) {
            $response =  get_parsed_validation_error_response($validator);
            return response()->json($response, 400);
        }else{
            try{
                $user = JWTAuth::toUser($request->token);
                if($user){
                    $data = $request->only('group_name');
                    $file = [];
                    $file['image'] = $request->file('group_icon');

                    $group = $this->userGroupRepository->create([
                        'group_name' => $data['group_name'],
                        'user_id' => $user->id
                    ]);

                    if($group){

                        if(isset($file['image']))
                        {
                            $filePath = 'group';
                            $fileName = 'GIMG_'.$user->id."_".mt_rand();
                            $fileResult = uploadFile($file['image'], $filePath, $fileName);
                            $group->group_icon = $fileResult;
                            $group->group_icon = str_replace('public/','',$group->group_icon);
                            $group->save();
                        }

                        //auto attached to group as member of group
                        $group->members()->attach($user->id);

                        $response = [
                            'status' => true,
                            'message' => 'Successfully created group',
                            'group_id' => $group['id']
                        ];
                    }
                }
            }catch(\Exception $e) {
                $response = error_reponse_handler($e);
                return response()->json($response['response'],$response['status_code']);
            }
        }
        return response()->json($response,200);
    }

        /**
         * @api {post} /group/update  Update User Group
         * @apiName Update User Group
         * @apiGroup UserGroup
         *
         * @apiDescription Note : user body/form-data paramter option for this api otherwise file will not be uploaded.
         *
         * @apiParam {Number} group_id  group id.
         * @apiParam {String} [group_name]  group name.
         * @apiParam {File} [group_icon]  group icon.
         *
         * @apiHeader {String} Authorization='bearer bd970a05-0ec1-4412-8b28-657962f0f778'
         *
         * @apiSuccessExample Success-Response:
         *     HTTP/1.1 200 OK
         *  {
         *       "status": true,
         *       "message": "Successfully updated group"
         *  }
         *
         */
    public function update(Request $request){
        $response = ['status' => false, 'message' => 'Something Went wrong while processing'];
        $validator = Validator::make($request->all(), [
            // 'group_name' => 'required',
            'group_id' => 'required',
        ]);

        if ($validator->fails()) {
            $response =  get_parsed_validation_error_response($validator);
            return response()->json($response, 400);
        }else{
            try{
                $user = JWTAuth::toUser($request->token);
                if($user){
                    $data = $request->only('group_name','group_icon','group_id');
                    $group = $this->userGroupRepository->find($data['group_id']);
                    unset($data['group_id']);

                    if($group){

                        $file = [];
                        $file['image'] = $request->file('group_icon');

                        if(isset($file['image']))
                        {
                            //unlink old avatar image
                            if(!empty($group->group_icon)){
                                $old_image = base_path().'/public/uploads/'.$group->group_icon;
                                // $old_image = base_path().'/storage/app/public/'.$group->group_icon;
                                // if(file_exists($old_image) && $group->group_icon != "group_logo.png"){
                                //     unlink($old_image);
                                // }
                            }

                            $filePath = 'group';
                            $fileName = 'GIMG_'.$user->id."_".mt_rand();
                            $fileResult = uploadFile($file['image'], $filePath, $fileName);
                            $group->group_icon = $fileResult;
                            $group->group_icon = str_replace('public/','',$group->group_icon);
                        }

                        if(isset($data['group_name'])){
                            $group->group_name = $data['group_name'];
                        }

                        $group->save();

                        $response = [
                            'status' => true,
                            'message' => 'Successfully updated group',
                        ];

                    }else{
                        $response['message'] = "Group not found";
                        return response()->json($response,404);
                    }
                }
            }catch(\Exception $e) {
                $response = error_reponse_handler($e);
                return response()->json($response['response'],$response['status_code']);
            }
        }
        return response()->json($response,200);
    }

        /**
         * @api {delete} /group/delete  Delete User Group
         * @apiName Delete User Group
         * @apiGroup UserGroup
         *
         * @apiParam {Number} group_id  group id.
         *
         * @apiHeader {String} Authorization='bearer bd970a05-0ec1-4412-8b28-657962f0f778'
         *
         * @apiSuccessExample Success-Response:
         *     HTTP/1.1 200 OK
         *  {
         *       "status": true,
         *       "message": "Successfully updated group"
         *  }
         *
         */
    public function delete(Request $request){
        $response = ['status' => false, 'message' => 'Something Went wrong while processing'];
        $validator = Validator::make($request->all(), [
            'group_id' => 'required',
        ]);

        if ($validator->fails()) {
            $response =  get_parsed_validation_error_response($validator);
            return response()->json($response, 400);
        }else{
            try{
                $user = JWTAuth::toUser($request->token);

                if($user){
                    $data = $request->only('group_id');
                    $group = $this->userGroupRepository->find($data['group_id']);
                    if($group){
                        if($user->id == $group->user_id){
                            $group->members()->detach(); //dettach all members from group

                            //delete group icon
                            if(!empty($group->group_icon)){
                                $old_image = base_path().'/public/uploads/'.$group->group_icon;
                                // $old_image = base_path().'/storage/app/public/'.$group->group_icon;
                                // if(file_exists($old_image) && $group->group_icon != "group_logo.png"){
                                //     unlink($old_image);
                                // }
                            }

                            $resp = $this->userGroupRepository->delete($group->id);
                            if($resp){
                                $response = [
                                    'status' => true,
                                    'message' => 'Successfully deleted group',
                                ];
                            }
                        }else{
                            $response['message'] = "Only group creator can delete group";
                        }
                    }else{
                        $response['message'] = "Group not found";
                        return response()->json($response,404);
                    }
                }
            }catch(\Exception $e) {
                $response = error_reponse_handler($e);
                return response()->json($response['response'],$response['status_code']);
            }
        }
        return response()->json($response,200);
    }

        /**
         * @api {post} /group/member/add  Add Group Member
         * @apiName Add Group Member
         * @apiGroup UserGroupMember
         *
         * @apiParam {Number} group_id  group id.
         * @apiParam {String} user_name  group member name.
         * @apiParam {String} user_phone_no  group member contact.
         *
         * @apiHeader {String} Authorization='bearer bd970a05-0ec1-4412-8b28-657962f0f778'
         *
         * @apiSuccessExample Success-Response:
         *     HTTP/1.1 200 OK
         *  {
         *       "status": true,
         *       "message": "Member added successfully"
         *  }
         *
         */
    public function add_member(Request $request){
        $response = ['status' => false, 'message' => 'Something Went wrong while processing'];
        $validator = Validator::make($request->all(), [
            'group_id' => 'required',
            'user_name' => 'required',
            'user_phone_no' => 'required',
        ]);

        if ($validator->fails()) {
            $response =  get_parsed_validation_error_response($validator);
            return response()->json($response, 400);
        }else{
            try{
                $user = JWTAuth::toUser($request->token);

                if($user){
                    $data = $request->only('user_name','user_phone_no','group_id');
                    $group = $this->userGroupRepository->find($data['group_id']);
                    $data['user_phone_no'] = preg_replace("/[^0-9+]/", "",$data['user_phone_no']);

                    if($group){
                        if (substr($data['user_phone_no'], 0, 1) === '+')     {
                            $data['user_phone_no'] = $data['user_phone_no'];
                        }else if (substr($data['user_phone_no'], 0, 1) === '0'){
                            $data['user_phone_no']= $user->country_code.ltrim($data['user_phone_no'], '0');
                        }else{
                            $data['user_phone_no'] = $user->country_code.$data['user_phone_no'];
                        }

                        if($user->id == $group->user_id){

                            $member_exists = $this->userRepository->findOneFromArray([
                                'phone' => $data['user_phone_no']
                            ]);

							if($member_exists)
							{
								if($group->members->contains($member_exists->id)){
									$response['message'] ="Member already added";
								}
								else
								{
									$user_id = $member_exists->id;
									$group->members()->attach($user_id);
									$user_device = $this->userDeviceRepository->findOneFromArray([
										'user_id' => $user_id
									]);

									$msg_data = [
										'invited_user_name' => explode(" ",$member_exists->name)[0],
										'user_invited_by' => $user->name,
										'group_name' => $group->group_name,
										'user_phone_no' => $data['user_phone_no'],
									];

									if(!empty($user_device["player_id"]))
									{
										//send push notification
										$message_name = 'group_add_notification_message';
										$msg = get_message_text($message_name, $msg_data);
										sendMessage($user_device["player_id"],$msg,$type = "pending");
									}
									else
									{
										//send sms
										$message_name = 'group_add_text_message';
										$msg = get_message_text($message_name, $msg_data);
										twilio_send_sms($msg, $data['user_phone_no']);
									}
									$response = [];
									$response['status'] = true;
									$response['message'] ="Member added successfully";
								}
							}
							else
							{
								$new_member = $this->userRepository->create([
                                    'phone' => $data['user_phone_no'],
                                    'name' => $data['user_name'],
                                    'status' => 2 //pending status
                                ]);
                                $user_id = $new_member->id;
                                //send text message invite to join app
                                $msg_data = [
                                    'invited_user_name' => explode(" ",$data['user_name'])[0],
                                    'user_invited_by' => $user->name,
                                    'group_name' => $group->group_name,
                                    'user_phone_no' => $data['user_phone_no'],
                                ];
                                $message_name = 'group_add_text_message';
                                $msg = get_message_text($message_name, $msg_data);
                                //send sms
                                twilio_send_sms($msg, $data['user_phone_no']);

								$group->members()->attach($user_id);
                                $response['status'] = true;
                                $response['message'] ="Member added successfully";
							}

                        }else{
                            $response['message'] = "Only group creator can add member";
                        }
                    }else{
                        $response['message'] = "Group not found";
                        return response()->json($response,404);
                    }
                }
            }catch(\Exception $e) {
                $response = error_reponse_handler($e);
                return response()->json($response['response'],$response['status_code']);
            }
        }

		return response()->json($response,200);
    }

      /**
         * @api {post} /group/member/add_multiple  Add Group Multiple Members
         * @apiName Add Group Members
         * @apiGroup UserGroupMember
         *
         * @apiParam {Number} group_id  group id.
         * @apiParam {String} members  group member array [ 'user_name' => 'sadasd' , 'user_phone_no' => 'dsfdsf'  \].
         *
         * @apiHeader {String} Authorization='bearer bd970a05-0ec1-4412-8b28-657962f0f778'
         *
         * @apiSuccessExample Success-Response:
         *     HTTP/1.1 200 OK
         *  {
         *       "status": true,
         *       "message": "Member added successfully"
         *  }
         *
         */
        public function add_multiple_member(Request $request){
            $response = ['status' => false, 'message' => 'Something Went wrong while processing'];
            $validator = Validator::make($request->all(), [
                'group_id' => 'required',
                'members' => 'required|array'
            ]);

            if ($validator->fails()) {
                $response =  get_parsed_validation_error_response($validator);
                return response()->json($response, 400);
            }else{
                try{
                    $user = JWTAuth::toUser($request->token);

                    if($user){
                        $data = $request->only('members','user_name','user_phone_no','group_id');
                        $group = $this->userGroupRepository->find($data['group_id']);

                        if($group){

                            if($user->id == $group->user_id){

                                foreach($data['members'] as $user_member){

                                    $member_exists = $this->userRepository->findOneFromArray([
                                        'phone' => $data['user_phone_no']
                                    ]);

                                    if( $member_exists ){
                                        $member = $member_exists;
                                        $user_id = $member_exists->id;

                                        $user_device = $this->userDeviceRepository->findOneFromArray([
                                            'user_id' => $user_id
                                        ]);

                                        //send push notification
                                        $msg_data = [
                                            'invited_user_name' => explode(" ",$member->name)[0],
                                            'user_invited_by' => $user->name,
                                            'group_name' => $group->group_name,
                                            'user_phone_no' => $data['user_phone_no'],
                                        ];
                                        $message_name = 'group_add_notification_message';
                                        $msg = get_message_text($message_name, $msg_data);
//                                        $msg = "Hi ".explode(" ",$member->name)[0].", ".$user->name." just added you to their group (".$group->group_name.") with your number (".$data['user_phone_no'].") on UnstuQ.";
                                        //pending to be implement push notification

                                        sendMessage($user_device["player_id"],$msg,$type = "pending");

                                    }else{
                                        //add dummy account with user
                                        $new_member = $this->userRepository->create([
                                            'phone' => $user_member['user_phone_no'],
                                            'name' => $user_member['user_name'],
                                            'status' => 2 //pending status
                                        ]);
                                        $member = $new_member;
                                        $user_id = $new_member->id;
                                        //send text message invite to join app
                                        $msg_data = [
                                            'invited_user_name' => explode(" ",$user_member['user_name'])[0],
                                            'user_invited_by' => $user->name,
                                            'group_name' => $group->group_name,
                                            'user_phone_no' => $data['user_phone_no'],
                                        ];
                                        $message_name = 'group_add_text_message';
                                        $msg = get_message_text($message_name, $msg_data);
//                                        $msg = "Hi ".explode(" ",$user_member['user_name'])[0].", ".$user->name." just added you to their group (".$group->group_name.") with your number (".$data['user_phone_no'].") on UnstuQ. Come join the excitement and see what the buzz is all about. Download the app here. https://unstuq.com";
                                        //send sms
                                        twilio_send_sms($msg, $user_member['user_phone_no']);
                                    }

                                    //check if member already added
                                    if($group->members->contains($member->id)){
                                        // $response['message'] ="Member already added";
                                    }else{
                                        $group->members()->attach($member->id);
                                        $response['status'] = true;
                                        $response['message'] ="Member added successfully";
                                    }
                                }

                            }else{
                                $response['message'] = "Only group creator can add member";
                            }
                        }else{
                            $response['message'] = "Group not found";
                            return response()->json($response,404);
                        }
                    }
                }catch(\Exception $e) {
                    $response = error_reponse_handler($e);
                    return response()->json($response['response'],$response['status_code']);
                }
            }
            return response()->json($response,200);
        }

        /**
         * @api {delete} /group/member/delete  Delete Group Member
         * @apiName Delete Group Member
         * @apiGroup UserGroupMember
         *
         * @apiParam {Number} group_id  group id.
         * @apiParam {String} user_id  group member.
         *
         * @apiHeader {String} Authorization='bearer bd970a05-0ec1-4412-8b28-657962f0f778'
         *
         * @apiSuccessExample Success-Response:
         *     HTTP/1.1 200 OK
         *  {
         *       "status": true,
         *       "message": "Member removed successfully"
         *  }
         *
         */
    public function delete_member(Request $request){
        $response = ['status' => false, 'message' => 'Something Went wrong while processing'];
        $validator = Validator::make($request->all(), [
            'group_id' => 'required',
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            $response =  get_parsed_validation_error_response($validator);
            return response()->json($response, 400);
        }else{
            try{
                $user = JWTAuth::toUser($request->token);

                if($user){
                    $data = $request->only('group_id','user_id');
                    $group = $this->userGroupRepository->find($data['group_id']);
                    if($group){
                        if($user->id == $group->user_id){
                            if($data['user_id'] != $user->id){
                                //delete member
                                if($group->members->contains($data['user_id'])){
                                    $group->members()->detach($data['user_id']);
                                    $response['status'] = true;
                                    $response['message'] ="Member removed successfully";
                                }else{
                                    $response['message'] ="User not member of group";
                                }
                            }else{
                                $response['message'] = "Group creator can not delete themself.";
                            }
                        }else{
                            $response['message'] = "Only group creator can delete group";
                        }

                    }else{
                        $response['message'] = "Group not found";
                        return response()->json($response,404);
                    }
                }
            }catch(\Exception $e) {
                $response = error_reponse_handler($e);
                return response()->json($response['response'],$response['status_code']);
            }
        }
        return response()->json($response,200);
    }

        /**
         * @api {post} /group/exit  Exit User Group
         * @apiName Exit User Group
         * @apiGroup UserGroupMember
         *
         * @apiParam {Number} group_id  group id.
         *
         * @apiHeader {String} Authorization='bearer bd970a05-0ec1-4412-8b28-657962f0f778'
         *
         * @apiSuccessExample Success-Response:
         *     HTTP/1.1 200 OK
         *  {
         *       "status": true,
         *       "message": "Successfully exit group"
         *  }
         *
         */
        public function exit_group(Request $request){
            $response = ['status' => false, 'message' => 'Something Went wrong while processing'];
            $validator = Validator::make($request->all(), [
                'group_id' => 'required',
            ]);

            if ($validator->fails()) {
                $response =  get_parsed_validation_error_response($validator);
                return response()->json($response, 400);
            }else{
                try{
                    $user = JWTAuth::toUser($request->token);

                    if($user){
                        $data = $request->only('group_id');
                        $group = $this->userGroupRepository->find($data['group_id']);

                        if($group){
                            $user_id = $user->id;
                            if($user_id != $group->user_id){
                                if($group->members->contains($user_id)){
                                    $group->members()->detach($user_id);
                                    $response['status'] = true;
                                    $response['message'] ="Successfully exit group";
                                }else{
                                    $response['message'] ="User not member of group";
                                }
                            }else{
                                $response['message'] = "Group creator can not exit group.";
                            }
                        }
                    }
                }catch(\Exception $e) {
                    $response = error_reponse_handler($e);
                    return response()->json($response['response'],$response['status_code']);
                }
            }
            return response()->json($response,200);
        }

        /**
         * @api {get} /group/all  Get All Groups
         * @apiName Get Groups
         * @apiGroup UserGroup
         *
         * @apiHeader {String} Authorization='bearer bd970a05-0ec1-4412-8b28-657962f0f778'
         *
         * @apiSuccessExample Success-Response:
         *     HTTP/1.1 200 OK
         *  {
         *       "status": "1",
         *       "data": [
         *          {
         *             "id": 1,
         *             "name": "test group",
         *             "icon": "uploads/group/GIMG_1_1870712292.jpeg",
         *             "created_by": 1,
         *             "created_at": "2020-06-17 15:24:28",
         *             "members_count": 1
         *         }
         *     ]
         *  }
         *
         */
    public function get_all_groups(Request $request){
        $response = ['status' => false, 'message' => 'Something Went wrong while processing'];
        try{
            $user = JWTAuth::toUser($request->token);
            if($user){
                $groups = $user->groups;

                $groupArray = [];
                foreach($groups as $group){
                    $members = $group->members;
                    $members_count = count($group->members);
                    $group_arr = $group->toArray();
                    $group_arr['members_count'] =  $members_count;
                    $group_arr['members'] = $members;
                    $groupArray[] =  $group_arr;
                }

                $response = [
                    'status' => true,
                    'data' =>  $groupArray
                ];
            }
        }catch(\Exception $e) {
            $response = error_reponse_handler($e);
            return response()->json($response['response'],$response['status_code']);
        }

        return response()->json($response,200);
    }

        /**
         * @api {get} /group/member/all  Get All Group Members
         * @apiName Get All Group Members
         * @apiGroup UserGroupMember
         *
         * @apiHeader {String} Authorization='bearer bd970a05-0ec1-4412-8b28-657962f0f778'
         * @apiParam {Number} group_id  group id.
         *
         * @apiSuccessExample Success-Response:
         *     HTTP/1.1 200 OK
         *  {
         *       "status": "1",
         *       "data":[
         *         {
         *             "id": 1,
         *             "name": "test 123",
         *             "avatar": "uploads/user/IMG_1_1218437384.jpg",
         *             "email": "neha.bhole2008@gmail.com",
         *             "phone": "+918879676620",
         *             "status": "active",
         *             "created_at": "2020-06-12 10:04:28"
         *         }
         *     ]
         *  }
         *
         */
    public function get_group_members(Request $request){
        $response = ['status' => false, 'message' => 'Something Went wrong while processing'];

        $validator = Validator::make($request->all(), [
            'group_id' => 'required',
        ]);

        if ($validator->fails()) {
            $response =  get_parsed_validation_error_response($validator);
            return response()->json($response, 400);
        }else{
            try{
                $user = JWTAuth::toUser($request->token);

                if($user){
                    $data = $request->only('group_id');
                    $group = $this->userGroupRepository->find($data['group_id']);

                    if($group){
                        $members = $group->members;
                        $response = [
                            'status' => true,
                            'data' =>  [
                                'members' => $members,
                                'group_info' => $group
                            ]
                        ];
                    }else{
                        $response['message'] = "Group not found";
                        return response()->json($response,404);
                    }
                }
            }catch(\Exception $e) {
                $response = error_reponse_handler($e);
                return response()->json($response['response'],$response['status_code']);
            }
        }
        return response()->json($response,200);
    }

    /**
     * @api {get} /crew/member/get  Get  Crew Member
     * @apiName Get crew Member
     * @apiGroup UserGroup
     *
     * @apiHeader {String} Authorization='bearer bd970a05-0ec1-4412-8b28-657962f0f778'
     * @apiParam {Number} crew_member_id  crew member id.
     * @apiParam {Number} group_id  group id.
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *  {
     *       "status": "1",
     *       "data":[
     *         {
     *             "id": 1,
     *             "name": "test 123",
     *             "avatar": "uploads/user/IMG_1_1218437384.jpg",
     *             "email": "neha.bhole2008@gmail.com",
     *             "phone": "+918879676620",
     *             "status": "active",
     *             "created_at": "2020-06-12 10:04:28",
     *             'pending' => [],
     *             'matched' => []
     *         }
     *     ]
     *  }
     *
     */
    public function get_crew_members(Request $request){
        $response = ['status' => false, 'message' => 'Something Went wrong while processing'];

        $validator = Validator::make($request->all(), [
            'crew_member_id' => 'required',
            'group_id' => 'required',
        ]);

        if ($validator->fails()) {
            $response =  get_parsed_validation_error_response($validator);
            return response()->json($response, 400);
        }else{
            try{
                $users = JWTAuth::toUser($request->token);
                $search_event_types = config('constant.search_event_types');
                $search_types = config('constant.search_types');

                if($users){
                    $data = $request->only('crew_member_id','group_id');
                    $user = $this->userRepository->find($data['crew_member_id'])->toArray();
                    //dd($user);
                    $user_group_member = $this->userGroupMemberRepository->findOneFromArray([
                        ['user_id', '=', $user['id']],
                        ['user_group_id','=', $data['group_id']]
                    ]);
                    $user_group = $this->userGroupRepository->findOneFromArray([
                        ['id','=', $data['group_id'] ]
                    ]);
                    //dd($user_group_member,$user_group);
                    if($user_group_member){
                        $user['is_added_in_group'] = true;
                        $user['group_id'] = $user_group['id'];
                        $user['group_name'] = $user_group['group_name'];
                        $user['added_in_group_at'] = $user_group_member['created_at'];
                        $today = date('Y-m-d');
                        $user_added_group_created_at = date('Y-m-d',strtotime($user['added_in_group_at']));
                        $date1=date_create($today);
                        $date2=date_create($user_added_group_created_at);
                        $diff=date_diff($date1,$date2);
                        $user['days_ago'] = (int)$diff->format("%a");
                    }else{
                        $user['is_added_in_group'] = false;
                        $user['group_id'] = 0 ;
                        $user['group_name'] = '';
                        $user['added_in_group_at'] = '';
                        $user['days_ago'] = 0;

                    }


                    //get only last 30 days match results
                    $x_days = ( 30 * 24 * 60 * 60 );
                    $x_days_before = strtotime(gmdate("Y-m-d H:i:s")) -  $x_days;
                    $check = gmdate("Y-m-d H:i:s",$x_days_before);
                    $today =date("Y-m-d H:i:s");

                    /************* Start pending search  *********/
                    $users_arr = [];
                    $users_arr[] =  $users->id;
                    $users_arr[] = $data['crew_member_id'];
                    $groups= $this->userGroupMemberRepository->get_groups($users_arr);
                    $grps_arr = [];
                    foreach($groups as $grp){
                        $grps_arr[] =$grp->user_group_id;
                    }

                    $pending_searches = $this->searchTransactionRepository->get_pending_matched_crew_data($status = 'pending',$today,$users_arr);
                   /* $pending_searches = $this->searchTransactionRepository->findMultipleFromArray([
                        ['status', '=', '0'],
                        ['user_id', '=',$data['crew_member_id'] ],
                        ['deadline', '>=',$today],
                    ]);
                    */
                    $pending_data = [];

                    if($pending_searches){
                        if(count($pending_searches) == 0){
                            $pending_data = [];
                        }else{
                            foreach($pending_searches as $search){
                                $details = $search->results; //already parsed data saved in database

                                //get any user who are not avialable or skipped the event
                                $skippedUsers = $this->searchTransactionUserRepository->findMultipleFromArray([
                                    'search_transaction_id' => $search->id
                                ])->toArray();

                                //get users attending to event
                                $unique_members = get_search_users_attending_event($search, $skippedUsers);
                                $group_array = [];
                                $groupUsers = $this->searchTransactionGroupRepository->getgroupdetails($search->id);
                                if($groupUsers) {
                                    foreach ($groupUsers as $grp) {
                                        unset($grp->user_group_id);
                                        unset($grp->search_transaction_id);
                                        foreach ($grp->user_group as $user_grp) {
                                            $group_array = [];
                                            $group_array['id'] = $user_grp['id'];
                                            $group_array['name'] = $user_grp['group_name'];

                                        }
                                    }
                                }
                                //reviews
                                $reviews = 0;
                                $pending_data[] = [
                                    'search_id' => $search->id,
                                    'type' => $search_event_types[$search->search_type],
                                    'search_user_type' => $search_types[$search->search_user_type],
                                    'search_title' => $search->search_title,//pending to do like solo dine out,dine out with family group
                                    'meet_time' => $search->meet_time,
                                    'location_name' => $search->location_name == null ? "N/A" : $search->location_name,
                                    'group_id' => isset($group_array['id']) ? $group_array['id'] : 0,
                                    'group_name' => isset($group_array['name']) ? $group_array['name'] : '',
                                   'is_response_submitted' => $this->matchMakerRepository->get_like_dislike_status($users->id,$search->id),
                                    'users' => $unique_members,
                                    'reviews' => $reviews
                                ];
                            }
                            usort($pending_data, function($a, $b) {
                                return $b['search_id'] <=> $a['search_id'];
                            });

                        }
                    }
                    $user['pending_data'] = $pending_data;
                    //end pending search

                    //check search_id exists
                    $matched_searches = $this->searchTransactionRepository->get_pending_matched_crew_data($status = 'match',$check,$users_arr);
                    /*$matched_searches = $this->searchTransactionRepository->findMultipleFromArray([
                        ['status', '=', '1'],
                        ['created_at', '>=',$check ],
                        ['user_id', '=',$data['crew_member_id'] ],
                    ]);*/
                    $matched_data = [];

                    if($matched_searches){
                        if(count($matched_searches) == 0){
                            $matched_data = [];
                        }else{

                            foreach($matched_searches as $search){

                                $searchType = "";
                                if($search->network == '' || $search->network == null)
                                {
                                    $searchType = "location";
                                    $details = json_decode($search->results);  //already parsed data saved in database
                                    $matched_details = '';
                                    foreach($details->results as $key => $detail){
                                        if(isset($detail->location_id)){
                                            if($detail->location_id == $search->matched_entity_id){
                                                $matched_details = json_decode(json_encode($detail), true);
                                            }
                                        }
                                    }
                                }
                                else
                                {
                                    $searchType = "movie";
                                    $details = json_decode($search->results);  //already parsed data saved in database
                                    $matched_details = '';
                                    foreach($details->results as $key => $object){
                                        if(isset($object->id)){
                                            if($object->id == $search->matched_entity_id){
                                                $matched_details = json_decode(json_encode($object), true);
                                            }
                                        }
                                    }
                                }

                                //get any user who are not avialable or skipped the event
                                $skippedUsers = $this->searchTransactionUserRepository->findMultipleFromArray([
                                    'search_transaction_id' => $search->id
                                ])->toArray();

                                $group_array = [];
                                $groupUsers = $this->searchTransactionGroupRepository->getgroupdetails($search->id);
                                if($groupUsers) {
                                    foreach ($groupUsers as $grp) {
                                        unset($grp->user_group_id);
                                        unset($grp->search_transaction_id);
                                        foreach ($grp->user_group as $user_grp) {
                                            $group_array = [];
                                            $group_array['id'] = $user_grp['id'];
                                            $group_array['name'] = $user_grp['group_name'];

                                        }
                                    }
                                }

                                //get users attending to event
                                $unique_members = get_search_users_attending_event($search, $skippedUsers);

                                if(count($matched_details)>0)
                                {
                                    if($searchType == "location")
                                    {
                                        $locationName = "";

                                        if(isset($matched_details["name"]))
                                        {
                                            $locationName = $matched_details["name"];
                                        }
                                        //reviews
                                        $reviews = $matched_details["num_reviews"]*1;
                                        $entity = ''.$search->matched_entity_id.'';

                                        $matched_data[] = [
                                            'search_id' => $search->id,
                                            'type' => $search_event_types[$search->search_type],
                                            'search_user_type' => $search_types[$search->search_user_type],
                                            'search_title' => $search->search_title,
                                            'meet_time' => $search->meet_time,
                                            'location_name' => $search->location_name,
                                            'matched_location' => $locationName,
                                            'matched_entity_id' => $entity,
                                            'group_id' => isset($group_array['id']) ? $group_array['id'] : 0,
                                            'group_name' => isset($group_array['name']) ? $group_array['name'] : '',
                                            'is_response_submitted' => $this->matchMakerRepository->get_like_dislike_status($users->id,$search->id),
                                            'users' => $unique_members,
                                            'reviews' => $reviews
                                        ];
                                    }
                                    else
                                    {
                                        $movieName = "";
                                        if(isset($matched_details["title"]))
                                        {
                                            $movieName = $matched_details["title"];
                                        }
                                        if(isset($matched_details["name"]))
                                        {
                                            $movieName = $matched_details["name"];
                                        }
                                        $entity = ''.$matched_details['id'].'';
                                        $matched_data[] = [
                                            'search_id' => $search->id,
                                            'type' => $search_event_types[$search->search_type],
                                            'search_user_type' => $search_types[$search->search_user_type],
                                            'search_title' => $search->search_title,
                                            'meet_time' => $search->meet_time,
                                            'location_name' => "N/A",
                                            'matched_location' => $movieName,
                                            'matched_entity_id' => $entity,
                                            'group_id' => isset($group_array['id']) ? $group_array['id'] : 0,
                                            'group_name' => isset($group_array['name']) ? $group_array['name'] : '',
                                           'is_response_submitted' => $this->matchMakerRepository->get_like_dislike_status($users->id,$search->id),
                                            'users' => $unique_members,
                                            'reviews' => $matched_details['vote_count'],
                                        ];
                                    }
                                }
                            }

                            usort($matched_data, function($a, $b) {
                                return $b['search_id'] <=> $a['search_id'];
                            });
                        }
                    }
                    $user['matched_data'] = $matched_data;

                    $response = [
                        'status' => true,
                        'data' =>  [
                            'crew_details' => $user,
                            // 'group_info' => $group
                        ]
                    ];
                }else{
                    $response['message'] = "Group not found";
                    return response()->json($response,404);
                }

            }catch(\Exception $e) {
                $response = error_reponse_handler($e);
                return response()->json($response['response'],$response['status_code']);
            }
        }
        return response()->json($response,200);
    }


}
