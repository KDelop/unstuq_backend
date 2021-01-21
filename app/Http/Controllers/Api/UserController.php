<?php

namespace App\Http\Controllers\Api;

use App\Models\UserDevice;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\UserDevice\UserDeviceRepositoryInterface;
use App\Repositories\UserFeedback\UserFeedbackRepositoryInterface;
use App\Repositories\UserFavorite\UserFavoriteRepositoryInterface;
use App\Repositories\Business\BusinessRepositoryInterface;
use App\Repositories\Movies\MovieRepositoryInterface;
use App\Repositories\UserGroupMember\UserGroupMemberRepositoryInterface;
use App\Repositories\SearchTransaction\SearchTransactionRepositoryInterface;
use App\Repositories\SearchTransactionUser\SearchTransactionUserRepositoryInterface;
use App\Repositories\SearchTransactionGroup\SearchTransactionGroupRepositoryInterface;
use App\Models\source;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Validator;
use App\Models\UserSource;
use App\Models\UserCredit;
use App\Models\PremiumUsers;
use App\Models\SearchOption;
use App\Models\UserFavourite;
class UserController extends Controller
{
    private $userRepository;
    private $userGroupMemberRepository;
    private $userDeviceRepository;
    private $userFeedbackRepository;
    private $userFavoriteRepository;
    private $businessRepository;
    private $searchTransactionRepository;
    private $searchTransactionUserRepository;
    private $movieRepository;
    private $searchTransactionGroupRepository;
    private $user;
    private $search_transaction_id;

    public function __construct(UserRepositoryInterface $userRepository,
        UserGroupMemberRepositoryInterface $userGroupMemberRepository,
                                UserDeviceRepositoryInterface $userDeviceRepository,
    UserFeedbackRepositoryInterface $userFeedbackRepository,
    UserFavoriteRepositoryInterface $userFavoriteRepository,
    BusinessRepositoryInterface $businessRepository,
                                SearchTransactionRepositoryInterface $searchTransactionRepository,
                                SearchTransactionUserRepositoryInterface $searchTransactionUserRepository,
    MovieRepositoryInterface $movieRepository,
    searchTransactionGroupRepositoryInterface $searchTransactionGroupRepository
    ){
        $this->userRepository = $userRepository;
        $this->userGroupMemberRepository = $userGroupMemberRepository;
        $this->userDeviceRepository = $userDeviceRepository;
        $this->userFeedbackRepository = $userFeedbackRepository;
        $this->userFavoriteRepository = $userFavoriteRepository;
        $this->businessRepository = $businessRepository;
        $this->searchTransactionRepository = $searchTransactionRepository;
        $this->searchTransactionUserRepository = $searchTransactionUserRepository;
        $this->movieRepository = $movieRepository;
        $this->searchTransactionGroupRepository = $searchTransactionGroupRepository;
        $this->middleware('jwt.auth');
    }

        /**
         * @api {get} /home  User Home page
         * @apiName home page
         * @apiGroup User
         *
         * @apiParam {String} location_lat  user lat location.
         * @apiParam {String} location_long  user long location.
         * @apiParam {String} [player_id]  user player id.
         *
         * @apiHeader {String} Authorization='bearer bd970a05-0ec1-4412-8b28-657962f0f778'
         *
         * @apiSuccessExample Success-Response:
         *     HTTP/1.1 200 OK
         *  {
         *       "status": true,
         *       "data": {
         *            "restaurants" : [],
         *            "attractions" : []
         *        }
         *  }
         *
         *  @apiErrorExample Error-Response:
         *     HTTP/1.1 200 Not Found
         *     {
         *        "status": false,
         *        "message": "lat long not provided"
         *     }
         *
         *  @apiErrorExample Error-Response:
         *     HTTP/1.1 200 Not Found
         *    {
         *       "status": false,
         *        "message": "Authorization Token not found"
         *     }
         *
         *  @apiErrorExample Error-Response:
         *     HTTP/1.1 200 Not Found
         *    {
         *       "status": false,
         *        "message": "Token is Invalid"
         *     }
         *
         */
    public function home(Request $request){
        $response = ['status' => false, 'message' => 'Something Went wrong while processing'];
        $validator = Validator::make($request->all(), [
            //'location' => 'required',
            // 'location_long' => 'required',
        ]);

        if ($validator->fails()) {
            $response =  get_parsed_validation_error_response($validator);
            return response()->json($response, 400);
        }else{
            try{
                if(isset($request["location_lat"]) && isset($request["location_long"]))
                {
                    $url = "http://api.positionstack.com/v1/reverse?access_key=4a098b030628a76a82c024bc7d0dcfc0&query=".$request["location_lat"].",".$request["location_long"];
                    $locationData = json_decode(file_get_contents($url),true);
                    $request['location'] = $locationData["data"][0]["locality"].", ".$locationData["data"][0]["region"].", ".$locationData["data"][0]["country"];                    
                }

                $request['searchType']='discover';
                $request['type']='restaurants,events,movies_in_theaters,activities';
                $user = JWTAuth::toUser($request->token);
                if($user){
                    $x_days = ( 30 * 24 * 60 * 60 );
                    $x_days_before = strtotime(gmdate("Y-m-d H:i:s")) -  $x_days;
                    $check = gmdate("Y-m-d H:i:s",$x_days_before);
                    $today =date("Y-m-d H:i:s");
                    $groups= $this->userGroupMemberRepository->findMultipleFromArray([
                        ['user_id', '=',$user->id],
                    ]);

                    $grps_arr = [];
                    foreach($groups as $grp){
                        $grps_arr[] =$grp->user_group_id;
                    }
                    
                    $pending_searches = $this->searchTransactionRepository->get_pending_data($grps_arr,$status = 'pending',$today, $user->id);
					
					$pending_result_count = 0;
					if(count($pending_searches) > 0){
						foreach($pending_searches as $search){
							$skippedUsers = $this->searchTransactionUserRepository->findMultipleFromArray([
								'search_transaction_id' => $search->id
							])->toArray();
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
							$isUniqueMember = array_filter($unique_members,function ($e) use (&$user){return $e["id"] == $user->id;});
							if($isUniqueMember)
							{
								$pending_result_count++;
							}
						}
					}
                    $trending_count = config('constant.trending_count');
                    $min_favorite_count = config('constant.min_favorite_count');
                    $dine_out_fav = $activity_fav = [];
                    $data['currency'] = 'USD';
                    $data['lang'] = "en-US";
                    $data['longitude'] = $request->input('location_long');
                    $data['latitude'] = $request->input('location_lat');
                    $data['offset'] = 0;
					$data['distance'] = 15;
					$data['lunit'] = 'mi';
					$data['api_key'] =  env('API_KEY');
                    if(!empty($request->input('player_id'))){
                        $this->userDeviceRepository->update_player_id($user->id,$request->input('player_id'));
                    }
                    $restaurant_array=array();
                    $attraction_array=array();
                    $movie_array=array();
                    $events_array=array();
                    
                    if(isset($request->location)){
                        $res = app()->call('App\Http\Controllers\Api\ActivityController@search',[$request]);
                        //return $res;
                        if(isset($res->getData()->data->restaurants)){
                            $restaurant_array[] = $res->getData()->data->restaurants;
                        }
                        if(isset($res->getData()->data->events)){
                            $events_array[] = $res->getData()->data->events;
                        }
                        if(isset($res->getData()->data->movies_in_theaters)){
                            $movie_array[] = $res->getData()->data->movies_in_theaters;
                        }
                        $attraction_array = array();
                        if(isset($res->getData()->data->activities)){
                            $attraction_array[] = $res->getData()->data->activities;
                        }
                    }
                    $groups = $user->groups;
                    $groupArray = [];
                    foreach($groups as $group){
                        $members_count = count($group->members);
                        $group_arr = $group->toArray();
                        $group_arr['members_count'] = $members_count;
                        $groupArray[] = $group_arr;
                    }
                    $userCredit=UserCredit::where('user_id',$user->id)->first();

                    if(!$userCredit){
                        UserCredit::create(['user_id'=>$user->id,'balance'=>10]);
                        $balance = 10;
                    }
                    else{
                        $balance = $userCredit->balance;
                    }
                    $response = [
                        'status' => true,
                        'data' => [
                            'restaurants' => $restaurant_array,
                            'attractions' => $attraction_array,
                            'movies_in_theaters' => $movie_array,
                            'events' => $events_array,
                            'user_groups' => $groupArray,
                            'pending_result_count' => $pending_result_count,
                            'credits'=>$balance,
                            'user'=>$user,
                        ]
                    ];

                }
            }catch(\Exception $e) {
                $response = error_reponse_handler($e);
                return response()->json($response['response'],$response['status_code']);
            }
        }
        return response()->json($response,200);
    }
    public function favourite_add(Request $request){
        $response = ['status' => false, 'message' => 'Something Went wrong while processing'];
        try{
            $validator = Validator::make($request->all(), [
                'searchType' => 'required',
                'data' => 'required',
            ]);
            if ($validator->fails()) {
                $response =  get_parsed_validation_error_response($validator);
                return response()->json($response, 400);
            }else{
                $entity_id = $request->searchType;
                $data = $request->data;
                $user = JWTAuth::toUser($request->token);
                if(isset(json_decode($data)->title)){
                    $title=json_decode($data)->title;
                }
                if(isset(json_decode($data)->name)){
                    $title=json_decode($data)->name;
                }
                $userFavourite = UserFavourite::where('user_id',$user->id)->where('entity_id',$entity_id)->where('title',$title)->first();
                
                if(!$userFavourite){
                    $userFavourite = UserFavourite::create([
                        'entity_id'=>$entity_id,
                        'user_id'=>$user->id,
                        'data'=>$data,
                        'title'=>$title
                    ]);
                    if($userFavourite){
                        $response = ['status' => true, 'message' => 'Entity added to user favourite list'];
                    }
                }else{
                    $response = ['status' => true, 'message' => 'Entity already added to user favourite list'];
                }
            }
        }catch(\Exception $e) {
            $response = error_reponse_handler($e);
            return response()->json($response['response'],$response['status_code']);
        }
        return response()->json($response,200);
    }
    public function favourite_get(Request $request){
        $response = ['status' => false, 'message' => 'Something Went wrong while processing'];
        try{
            $user = JWTAuth::toUser($request->token);
            if ($request->has('searchType')) {
                $entity_id = $request->searchType;
                $userFavourite = UserFavourite::where('user_id',$user->id)->where('entity_id',$entity_id)->where('deleted_at',null)->first();
            }else{
                $userFavourite = UserFavourite::where('user_id',$user->id)->where('deleted_at',null)->get();
            }
            $response['status']=false;
            if($userFavourite){
                $response['status']=true;
                $response['message']='Record fetched successfully';
            }else{
                $response['message']='Record not found';
            }
            $response['favourites']=$userFavourite;
            
        }catch(\Exception $e) {
            $response = error_reponse_handler($e);
            return response()->json($response['response'],$response['status_code']);
        }
        return response()->json($response,200);
    }

    public function favourite_delete(Request $request){
        $response = ['status' => false, 'message' => 'Something Went wrong while processing'];
        try{
            $validator = Validator::make($request->all(), [
                'id' => 'required',
            ]);
            if ($validator->fails()) {
                $response =  get_parsed_validation_error_response($validator);
                return response()->json($response, 400);
            }else{
                $id = $request->id;
                $userFavourite = UserFavourite::where('id',$id)->first();
                if($userFavourite){
                    $userFavourite->deleted_at = date('y-m-d');
                    $res = $userFavourite->save();
                    if($res){
                        $response = ['status' => true, 'message' => 'Entity removed from user favourite list'];
                    }
                }else{
                    $response = ['status' => false, 'message' => 'Entity not found in user favourite list'];
                }
            }
        }catch(\Exception $e) {
            $response = error_reponse_handler($e);
            return response()->json($response['response'],$response['status_code']);
        }
        return response()->json($response,200);        
    }

    public function get_search_option(Request $request){
        $searchType = $request->searchType;
        if(isset($request->searchType)){
            $searchOptions = SearchOption::where('searchType',$searchType)->where('active',1)->get();
        }else{
            $searchOptions = SearchOption::where('active',1)->get();     
        }
        $status=false;
        if(count($searchOptions)>0){
            $status=true;
        }
        $response=[
            'status'=>$status,
            'data'=>$searchOptions,
        ];
        return response()->json($response, 200);
    }
    public function add_premium_users(Request $request){
        $user = JWTAuth::toUser($request->token);
        $premiumUser = PremiumUsers::where('user_id',$user->id)->first();
        if(!$premiumUser){
            $premiumUser = PremiumUsers::create(['user_id'=>$user->id,'transaction_Date'=>date('Y-m-d')]);
        }
        $response=['data'=>$premiumUser,'message'=>'Premium user added','status'=>true];
        return response()->json($response, 200);
    }
    public function credits_add(Request $request){
        $validator = Validator::make($request->all(), [
            'credits' => 'required',
        ]);
        if ($validator->fails()) {
            $response =  get_parsed_validation_error_response($validator);
            return response()->json($response, 400);
        }else{
            $response=['status'=>false,'message'=>'Something went wrong'];
            $user = JWTAuth::toUser($request->token);
            $userCredit = UserCredit::where('user_id',$user->id)->first();
            if($userCredit){
                $userCredit->balance=$userCredit->balance+$request->credits;
                $userCredit->save();
                $response=['status'=>true,'message'=>'Credits successfully added','credits'=>$request->credits];
            }else{
                $userCredit = UserCredit::create(['user_id',$user->id,'balance'=>$request->credits+10]);
                if($userCredit){
                    $response=['status'=>true,'message'=>'credits successfully added','credits'=>$request->credits+10];
                }
            }
            return response()->json($response, 200);
        }
    }


        /**
         * @api {post} /user/profile/update  Update Profile Details
         * @apiName Update Profile Details
         * @apiGroup User
         *
         * @apiDescription Note : user body/form-data paramter option for this api otherwise file will not be uploaded.
         *
         * @apiparam {String} name user name
         * @apiparam {File} [image] Form-based Image Upload
         * @apiHeader {String} Authorization='bearer bd970a05-0ec1-4412-8b28-657962f0f778'
         *
         * @apiSuccessExample Success-Response:
         *     HTTP/1.1 200 OK
         *   {
         *     "status": true,
         *     "message": "Successfully Updated",
         *     "data": {
         *         "id": 1,
         *         "name": "Neha bhole",
         *         "avatar": "storage/user/IMG_1_1907101508.png",
         *         "email": "neha.bhole2008@gmail.com",
         *         "phone": "+918879676620",
         *         "status": "active",
         *         "created_at": "2020-06-12 10:04:28"
         *      }
         * }
         */
    public function update_profile_details(Request $request){
        $response = ['status' => false, 'message' => 'Something Went wrong while processing'];
        $result=true;
        $noChange=true;
        try{
            $user = JWTAuth::toUser($request->token);
            if($user){
                $validator = Validator::make($request->all(), [
                    'image' => 'max:10000',
                    'name' => 'required',
                    'sourceIds' => 'string'
                ]);

                if ($validator->fails()) {
                    $response =  get_parsed_validation_error_response($validator);
                    return response()->json($response, 400);
                }else{
                    $data = $request->only('name');

                    $file = [];
                    $file['image'] = $request->file('image');

                    if(isset($file['image']))
                    {
                        //unlink old avatar image
                        if(!empty($user->avatar)){
                            // $old_image = base_path().'/storage/app/public/'.$user->avatar;
                            $old_image = base_path().'/public/uploads/'.$user->avatar;
                            // if(file_exists($old_image)){
                            //     unlink($old_image);
                            // }
                        }

                        // $filePath = 'user';
                        // $fileName = 'IMG_'.$user->id."_".mt_rand();
                        // $fileResult = uploadFile($file['image'], $filePath, $fileName);
                        // $user->avatar = $fileResult;
                        // $user->avatar = str_replace('public/','',$user->avatar);
                        $user->avatar = 'https://unstuq-dev-media.s3.us-east-2.amazonaws.com/'.$request->image->store('profile_images', 's3');
                    }
                    if(isset($data['name'])){
                        $user->name = $data['name'];
                    }
                    $user->save();

                    $userId = $user->id;
                    if ($request->has('sourceIds')) {
                        $sourceIds = explode(',', $request->sourceIds);
                        $res = UserSource::where('user_id',$userId)->delete();
                        if(!$res){
                            $response = Array(['message'=>'Something went wrong','status'=>false]);
                            return response()->json($response, 200);
                        }
                        foreach($sourceIds as $key => $sourceId){
                            // $isUserSource = UserSource::where('user_id',$userId)->where('source_id',$sourceId)->first();
                            $userSource = UserSource::create([
                                'user_id'=>$userId,
                                'source_id'=>$sourceId
                            ]);
                            // $noChange=false;
                            // if(!$userSource){
                            //     $result = false;
                            // }
                        }
                    }
                    

                    $this->user = $this->userRepository->find($user->id);
                    $data = $this->user->toArray();
                    $sources = source::with(['UserSource' => function($q){
                        $q->where('user_id', $this->user->id);
                    }])->get();
                    // return json_encode($sources);die();
                    $data['sources'] = $sources;
                    $response = [
                        'status' => true,
                        'message' =>'Successfully Updated',
                        'data' => $data
                    ];
                }
            }
        }catch(\Exception $e) {
            $response = error_reponse_handler($e);
            return response()->json($response['response'],$response['status_code']);
        }
            return response()->json($response,200);

    }

        /**
         * @api {get} /user/profile/get  Get Profile Details
         * @apiName Get Profile Details
         * @apiGroup User
         *
         * @apiHeader {String} Authorization='bearer bd970a05-0ec1-4412-8b28-657962f0f778'
         *
         * @apiSuccessExample Success-Response:
         *     HTTP/1.1 200 OK
         *   {
         *     "status": true,
         *     "data": {
         *        "id": 1,
         *         "name": "neha bhole",
         *         "avatar": "storage/user/IMG_1.jpg",
         *         "email": "neha.bhole2008@gmail.com",
         *         "phone": "+911234567890",
         *         "status": 1,
         *         "created_at": "2020-06-10 09:33:28"
         *      }
         *   }
         */
    public function get_profile_details(Request $request){
        $response = ['status' => false, 'message' => 'Something Went wrong while processing'];
        try{
            $this->user = JWTAuth::toUser($request->token);
            if($this->user){
                $data = $this->user->toArray();
                // return $data;
                $sources = source::with(['UserSource' => function($q){
                    $q->where('user_id', $this->user->id);
                }])->get();
                $data['sources'] = $sources;
                $response = [
                    'status' => true,
                    'data' => $data
                ];
            }
        }catch(\Exception $e) {
            $response = error_reponse_handler($e);
            return response()->json($response['response'],$response['status_code']);
        }
        return response()->json($response,200);
    }

        /**
         * @api {get} /user/favorite/get  Get Favorites
         * @apiName Get Favorite
         * @apiGroup User
         *
         * @apiHeader {String} Authorization='bearer bd970a05-0ec1-4412-8b28-657962f0f778'
         * @apiParam {Number} type 1: restaurants, 2:attractions, 3:hotels, 4:movie | tv
         *
         * @apiSuccessExample Success-Response:
         *     HTTP/1.1 200 OK
         *   {
         *     "status": true,
         *     "data": {
         *        "favorites" : []
         *      }
         *   }
         */
    public function get_favorites(Request $request){
        $response = ['status' => false, 'message' => 'Something Went wrong while processing'];

        $response = ['status' => false, 'message' => 'Something Went wrong while processing'];
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:1,2,3,4,5'
        ]);

        if ($validator->fails()) {
            $response =  get_parsed_validation_error_response($validator);
            return response()->json($response, 400);
        }else{

            try{
                $user = JWTAuth::toUser($request->token);
                if($user){
                    $data = $request->only('type');
                    $favorites = $this->userRepository->get_favorites($user->id,$data['type']);
                    $type_name = '';
                    switch ($data['type']) {
                        case "1":
                            $type_name = "restaurants";
                            break;
                        case "2":
                            $type_name = "attractions";
                            break;
                        case "3":
                            $type_name = "hotels";
                            break;
                        case "4":
                            $type_name = "movie";
                            break;
                        case "5":
                            $type_name = "tv";
                            break;
                    }
                    if($favorites){
                        foreach($favorites as $key=>$fav){
                            $search = $this->searchTransactionRepository->findMultipleFromArray([
                                ['user_id', '=', $fav['user_id']],

                            ]);
                            $fav['search_title'] = isset($search['search_title']) ? $search['search_title'] : '';
                            if(in_array($type_name,[1,2,3])) {
                                $url = env('SEARCH_API_URL') . "/" . $type_name . "/get-details";
                                $data['api_key'] = env('API_KEY');

                                $data['currency'] = 'USD';
                                $data['lang'] = "en-US";
                                $data['location_id'] = $fav['entity_id'];
                                $host = env('SEARCH_API_HOST');
                                $key = env('SEARCH_API_KEY');

                                $headers = ['Content-Type: application/json',
                                    'x-rapidapi-host: ' . $host,
                                    'x-rapidapi-key: ' . $key];
                                $method = "GET";
                                $results = callAPI($method, $url, $data, $headers);
                                $location_details = get_parsed_location_detail($results);
                                $fav['location_name'] = $location_details['name'];
                                $fav['location_address'] = $location_details['address'];
                                $fav['location_string'] = $location_details['location_string'];
                                $fav['address_object'] = $location_details['address_obj'];
                            }else{

                            }
                        }
                    }else{
                        $favorites = [];
                    }
                   // dd($favorites);


                    //get entity info with favorites

                    $response = [
                        'status' => true,
                        'data' => [
                            'favorites' => $favorites
                        ]
                    ];
                }
            }catch(\Exception $e) {
                $response = error_reponse_handler($e);
                return response()->json($response['response'],$response['status_code']);
            }
        }

        return response()->json($response,200);
    }

        /**
         * @api {post} /user/favorite/add  Add Favorite
         * @apiName Add Favorite
         * @apiGroup User
         *
         * @apiHeader {String} Authorization='bearer bd970a05-0ec1-4412-8b28-657962f0f778'
         * @apiParam {String} type favorite location  type
         * @apiParam {String} entity_id  favorite location id
         * @apiParam {String} location_name  location_name
         * @apiParam {String} address  location address id
         * @apiParam {String} longitude  location longitude
         * @apiParam {String} latitude  location latitude
         * @apiParam {String} rating  location rating
         * @apiParam {String} ranking  location ranking
         * @apiParam {String} info  location info
         * @apiParam {String} image  location image url
         *
         * @apiSuccessExample Success-Response:
         *     HTTP/1.1 200 OK
         *   {
         *     "status": true,
         *     "message": "Successfully added favorite"
         * }
         */
    public function add_favorite(Request $request){
        $response = ['status' => false, 'message' => 'Something Went wrong while processing'];
        $validator = Validator::make($request->all(), [
            'entity_id' => 'required',
            'location_name' => 'required',
            'address' => 'required',
            'longitude' => 'required',
            'latitude' => 'required',
            'rating' => 'required',
            'ranking' => 'required',
            'info' => 'required',
            'type' => 'required',
            'image' => 'required'
        ]);

        if ($validator->fails()) {
            $response =  get_parsed_validation_error_response($validator);
            return response()->json($response, 400);
        }else{

            try{
                $user = JWTAuth::toUser($request->token);
                if($user){
                    $data = $request->only('entity_id','type');

                    $favorite = $this->userFavoriteRepository->create([
                        'entity_id' => $data['entity_id'],
                        'type' => $data['type'],
                        'user_id' => $user->id
                    ]);
                    //add entity id details if not present
                    if(in_array($data['type'],[1,2,3])) {
                        //check if exists
                        $exists = $this->businessRepository->findOneFromArray([
                            'location_id' => $data['entity_id'],
                            'type' => $data['type']
                        ]);
                        if(!$exists){
                            $business = $this->businessRepository->create([
                                'name' => $request->location_name,
                                'location_id' => $data['entity_id'],
                                'longitude' => $request->longitude,
                                'latitude' => $request->latitude,
                                'rating' => $request->rating,
                                'ranking' => $request->ranking,
                                'info' => $request->info,
                                'type' => $data['type'],
                            ]);
                        }else{
                            $response['note'] = "already added to buisness";
                        }
                    }
                    if(in_array($data['type'],[4,5])) {

                        $movie = $this->movieRepository->create([
                            'property_id' => $data['entity_id'],
                            'name' => $request->location_name,
                            'details' => $request->info
                        ]);
                    }



                        if($favorite){
                        $favorites = $this->userRepository->get_favorites($user->id ,$data['type']);
                        $response = [
                            'status' => true,
                            'message' => 'Successfully added favorite',
                            'data' => [
                                'favorites' => $favorites
                            ]
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
         * @api {delete} /user/favorite/delete  Delete Favorites
         * @apiName Delete Favorite
         * @apiGroup User
         *
         * @apiHeader {String} Authorization='bearer bd970a05-0ec1-4412-8b28-657962f0f778'
         * @apiParam {String} entity_ids  entity ids : 110311,2090808
         * @apiParam {Number} type  1: restaurants, 2:attractions, 3:hotels, 4:movie | tv
         *
         * @apiSuccessExample Success-Response:
         *     HTTP/1.1 200 OK
         *   {
         *     "status": true,
         *     "message": "Successfully Deleted"
         * }
         */
    public function remove_favorite(Request $request){
        $response = ['status' => false, 'message' => 'Something Went wrong while processing'];
        $validator = Validator::make($request->all(), [
            'entity_ids' => 'required',
            'type' => 'required',
        ]);

        if ($validator->fails()) {
            $response =  get_parsed_validation_error_response($validator);
            return response()->json($response, 400);
        }else{
            try{
                $user = JWTAuth::toUser($request->token);
                if($user){
                    $data = $request->only('entity_ids','type');
                    $entities =explode(',',$data['entity_ids']);
                    $resp = $this->userFavoriteRepository->remove_fav($user->id,$entities,$data['type']);
                    if($resp){
                        $response = [
                            'status' => true,
                            'message' => 'Successfully deleted favorites',
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
         * @api {post} /user/feedback/add  Add Feedback
         * @apiName Add Feedback
         * @apiGroup User
         *
         * @apiHeader {String} Authorization='bearer bd970a05-0ec1-4412-8b28-657962f0f778'
         * @apiParam {String} category 1 for bug,2 for enhancement
         *  @apiParam {String} message
         *
         * @apiSuccessExample Success-Response:
         *     HTTP/1.1 200 OK
         *   {
         *     "status": true,
         *     "message": "Successfully Added feedback"
         * }
         */
    public function add_feedback(Request $request){
        $response = ['status' => false, 'message' => 'Something Went wrong while processing'];
        $validator = Validator::make($request->all(), [
            'category' => 'required|in:1,2',
            'message' => 'required'
        ]);

        if ($validator->fails()) {
            $response =  get_parsed_validation_error_response($validator);
            return response()->json($response, 400);
        }else{
            try{
                $user = JWTAuth::toUser($request->token);
                if($user){
                    $data = $request->only('message','category');

                    $feedback = $this->userFeedbackRepository->create([
                        'category' => $data['category'],
                        'message' => $data['message'],
                        'user_id' => $user->id
                    ]);

                    if($feedback){
                        $feedbacks = $this->userRepository->get_feedbacks($user->id);
                        $response = [
                            'status' => true,
                            'message' => 'Successfully added feedback',
                            'data' => [
                                'feedbacks' => $feedbacks
                            ]
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
         * @api {get} /user/feedback/get  Get Feedback
         * @apiName Get Feedback
         * @apiGroup User
         *
         * @apiHeader {String} Authorization='bearer bd970a05-0ec1-4412-8b28-657962f0f778'
         *
         * @apiSuccessExample Success-Response:
         *     HTTP/1.1 200 OK
         *   {
         *     "status": true,
         *     "data": {
         *          "feedbacks" => []
         *      }
         * }
         */

    public function get_feedback(Request $request){

        $response = ['status' => false, 'message' => 'Something Went wrong while processing'];
       try{
            $user = JWTAuth::toUser($request->token);
            if($user){
                $feedbacks = $this->userRepository->get_feedbacks($user->id);
                $response = [
                    'status' => true,
                    'data' => [
                        'feedbacks' => $feedbacks
                    ]
                ];
            }
        }catch(\Exception $e) {
            $response = error_reponse_handler($e);
            return response()->json($response['response'],$response['status_code']);
        }

        return response()->json($response,200);
    }

    /**
     * @api {post} /user/skip/add  Add skip user
     * @apiName Add skip user
     * @apiGroup User
     *
     * @apiHeader {String} Authorization='bearer bd970a05-0ec1-4412-8b28-657962f0f778'
     * @apiParam {Number} search_transaction_id search transaction id
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *   {
     *     "status": true,
     *     "message" => 'Successfully added skipped user'
     * }
     */

    public function add_skip_user_old(Request $request){
        //array of skipped $skipped_users
        $response = ['status' => false, 'message' => 'Something Went wrong while processing'];
        try{
            $user = JWTAuth::toUser($request->token);
            if($user){
                $data = $request->only('search_transaction_id');
                $skipped_users = $this->searchTransactionUserRepository->create([
                    'user_id' => $user->id,
                    'search_transaction_id' => $data['search_transaction_id'],
                    'status' => 1
                ]);
                $response = [
                    'status' => true,
                    'message' => 'Successfully added skipped user',
                ];
            }
        }catch(\Exception $e) {
            $response = error_reponse_handler($e);
            return response()->json($response['response'],$response['status_code']);
        }
        return response()->json($response,200);
    }

    public function add_skip_user(Request $request){
        // return ['from add_skip_user'=>$request->user_id];
        // dd($request->search_transaction_id);
        $response = ['status' => false, 'message' => 'Something Went wrong while processing'];
        try{
            $status = true;
            $this->search_transaction_id = $request->search_transaction_id;
            if(isset($request->skipped_users)){
                $skipped_users = explode(',', $request->skipped_users);
                foreach($skipped_users as $key => $skipped_user){
                    $res = $this->skip_user_function($this->search_transaction_id,$skipped_user);
                    if(!$res){
                        $status=false;
                    }
                }
            }else{
                $user = JWTAuth::toUser($request->token);
                if($user){
                    $res = $this->skip_user_function($this->search_transaction_id,$user->id);
                    if(!$res){
                        $status=false;
                    }
                }
            }
            if($status){
                $response = [
                    'status' => true,
                    'message' => 'Successfully added skipped user',
                ];
            }else{
                $response = [
                    'status' => true,
                    'message' => 'Successfully added skipped user',

                ];
            }
            return response()->json($response,200);
        }catch(\Exception $e) {
            $response = error_reponse_handler($e);
            return response()->json($response['response'],$response['status_code']);
        }
    }
    public function skip_user_function($search_transaction_id,$user_id){
        $skipped_users = $this->searchTransactionUserRepository->create([
            'user_id' => $user_id,
            'search_transaction_id' => $search_transaction_id,
            'status' => 1

        ]);
        return $skipped_users;
    }
}