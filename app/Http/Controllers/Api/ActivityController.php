<?php
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\UserDevice\UserDeviceRepositoryInterface;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;
use App\Models\UserDevice;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\env;
use Illuminate\Support\Facades\Auth;
use App\Repositories\SearchTransaction\SearchTransactionRepositoryInterface;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use App\Models\movie;
use App\Models\source;
use App\Http\Resources\MovieCollection;
use Illuminate\Support\Facades\DB;
use App\Models\Experience;
use App\Models\UserCredit;
use App\Models\show;
use App\Models\UserExperience;
use App\Models\UserSource;
use App\Models\DateIdea;
use AmazonFileHelper;
use Storage;
use App\Traits\FormData;
// use App\Models\source;
// use App\Http\Resources\SourceCollection;
class ActivityController extends Controller{
    use FormData;
    private $searchTransactionRepository;
    protected $method='GET';
    protected $endpoint = "https://serpapi.com/search.json";
    protected $params = [];
    protected $rkey = '';
    protected $data = [];
    protected $status=true;
    protected $statusCode=200;
    protected $types;
    protected $type;
    protected $message='Search complete';
    protected $provider=null;
    protected $providers=null;
    protected $imagePrefix='';
    protected $deviceId=array();
    protected $user;
    protected $search_transaction_id;
    protected $result_count=0;
    private $userDeviceRepository;
    public function __construct(SearchTransactionRepositoryInterface $searchTransactionRepository,UserDeviceRepositoryInterface $userDeviceRepository){
       $this->searchTransactionRepository = $searchTransactionRepository; 
       $this->userDeviceRepository = $userDeviceRepository;
    }
    public function search(Request $request){
        
        $response = ['status' => false, 'message' => 'Something Went wrong while processing'];
        // return $request->all();
        $requiredFields=['type','searchType','searchTitle'];
        $optionalFields=[];
        $res = $this->preparePostData($request, $requiredFields, $optionalFields, false);
        if(!$res){
            return $res;
        }
        $validator = Validator::make($request->all(), [
            'type' => 'required',
        ]);
        if ($validator->fails()) {
            $response =  get_parsed_validation_error_response($validator);
            return response()->json($response, 400);
        }else{
            //try{
                //return $request;
                $this->types = explode(',', $request->input('type'));
                $this->providers = explode(',', $request->input('providers'));
                $userids=$request->input('userids');
                $deadline=$request->input('deadline');
                $this->params['api_key']=ENV("SERP_API_KEY","");
                $this->params['gl']=$request->input('gl');
                $this->params['hl']=$request->input('hl');
                $this->params['location']=$request->input('location');

                if($request->input('location_lat') !== null && $request->input('location_long') !== null)
                {
                    $url = "http://api.positionstack.com/v1/reverse?access_key=4a098b030628a76a82c024bc7d0dcfc0&query=".$request->input('location_lat').",".$request->input('location_long');
                    $locationData = json_decode(file_get_contents($url),true);
                    $this->params['location'] = $locationData["data"][0]["locality"].", ".$locationData["data"][0]["region"].", ".$locationData["data"][0]["country"];                    
                }
                if($this->checkType('events')){
                    if (isset($request->searchText)) {
                        $q = $request->searchText;
                    }else{
                        $q = "events near me";
                    }
                    $this->type='events';
                    $this->params['q']=$q;
                    $this->params['engine']='google_events';
                    $this->rkey='events_results';
                    $requiredFields=['location','latitude','longitude','searchDay','searchTime','searchText'];
                    $optionalFields=[];
                    if($request->searchType=='group'){
                        array_push($requiredFields, 'groupId');
                        array_push($requiredFields, 'deadline');
                    }
                    $res = $this->callAPI($request, $requiredFields, $optionalFields, false);
                    if(!$res){
                        return $res;
                    }
                }
                unset($this->params['engine']);
                if($this->checkType('shopping')){
                    $this->type='shopping';
                    $requiredFields=['providers'];
                    $optionalFields=[];
                    $res = $this->preparePostData($request, $requiredFields, $optionalFields, false);
                    // dd($res);
                    if(!$res){
                        return $res;
                    }
                    if($this->checkProviders('google')){
                        $q = env('GOOGLE_SHOPPING_TEXT',"");
                        $this->provider='google';
                        $this->params['q']=$q;
                        $this->rkey='shopping_results';
                        $res = $this->callAPI($request, $requiredFields, $optionalFields, false);
                        if(!$res){
                            return $res;
                        }
                    }
                    if($this->checkProviders('ebay')){
                        $q = env("EBAY_SHOPPING_TEXT","");
                        $this->provider='ebay';
                        $this->params['engine']='ebay';
                        $q=$request->searchText;
                        $this->params['_nkw']=$q;
                        $this->rkey='organic_results';
                        $res = $this->callAPI($request, $requiredFields, $optionalFields, false);
                        if(!$res){
                            return $res;
                        }
                    }
                    if($this->checkProviders('walmart')){
                        $q = env("WALMART_SHOPPING_TEXT","");
                        $this->provider='walmart';
                        $this->params['engine']='walmart';
                        $this->params['query']=$q;
                        $this->rkey='organic_results';
                        $res = $this->callAPI($request, $requiredFields, $optionalFields, false);
                        if(!$res){
                            return $res;
                        }
                    }
                    if($this->checkProviders('amazon')){
                        $res = $this->preparePostData($request, $requiredFields, $optionalFields, false);
                        if(!$res){
                            return $res;
                        }
                        $q = env("AMAZON_SHOPPING_TEXT","");
                        $this->provider='amazon';
                        $queryString = http_build_query([
                          'api_key' => ENV("AMAZON_API_KEY",""),
                          'type' => 'search',
                          'amazon_domain' => 'amazon.com',
                          'search_term' => $request->searchText
                        ]);
                        $ch = curl_init(sprintf('%s?%s', 'https://api.rainforestapi.com/request', $queryString));
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                        $api_result = curl_exec($ch);
                        curl_close($ch);
                        $data=json_decode($api_result);
                        $this->data[$this->type] = $data->search_results;
                        $this->result_count+=count($data->search_results);
                    }
                }
                if($this->checkType('restaurants')){
                    if (isset($request->searchText)) {
                        $q = $request->searchText;
                    }else{
                        $q = "open restaurants near me";
                    }
                    $this->params['tbm']='lcl';
                    $this->type='restaurants';
                    $this->params['q']=$q;
                    $this->rkey='local_results';
                    $requiredFields=['location','latitude','longitude','searchDay','searchTime','searchText'];
                    $optionalFields=[];
                    if($request->searchType=='group'){
                        array_push($requiredFields, 'groupId');
                        array_push($requiredFields, 'deadline');
                    }
                    $res = $this->callAPI($request, $requiredFields, $optionalFields, false);
                    if(!$res){
                        return $res;
                    }
                }
                if($this->checkType('movies_in_theaters')){
                    $ch = curl_init('https://api.themoviedb.org/3/movie/now_playing?api_key=e566c5e04b1e59df409ac8926a762f77');
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                    $api_result = curl_exec($ch);
                    curl_close($ch);
                    $data=json_decode($api_result);
                    $this->imagePrefix='https://image.tmdb.org/t/p/w500';
                    $this->data['movies_in_theaters'] = $data->results;
                }
                if($this->checkType('customSearch')){
                    $user = JWTAuth::toUser($request->token);
                    $userCredit = UserCredit::where('user_id',$user->id)->first();
                    if($userCredit->balance<1){
                        $response = Array(['message'=>'Insufficient credit','status'=>false,'credits'=>0]);
                        return response()->json($response, 200);
                    }
                    $this->type='customSearch';
                    $requiredFields=['searchDay','searchTime','searchOptions'];
                    $tsearchOptions=[];
                    $key=0;
                    foreach ($request->image as $imaget) {
                        $tsearchOptions[$key]['name']=$request->searchOptions[$key]['name'];
                        $tsearchOptions[$key]['description']=$request->searchOptions[$key]['description'];
                        $newImagePath = 'https://unstuq-dev-media.s3.us-east-2.amazonaws.com/'.$imaget->store('custom_search_images', 's3');
                        $tsearchOptions[$key]['image']=$newImagePath;
                        $key++;
                    }
                    $optionalFields=[];
                    if($request->searchType=='group'){
                        array_push($requiredFields, 'groupId');
                        array_push($requiredFields, 'deadline');
                    }
                    $res = $this->preparePostData($request, $requiredFields, $optionalFields, false);
                    $this->result_count+=count($res['searchOptions']);
                    $tdata=$request->all();
                    // $this->data[$this->type]['type']=$request->type;
                    // $this->data[$this->type]['searchType']=$request->searchType;
                    // $this->data[$this->type]['groupId']=$request->groupId;
                    // $this->data[$this->type]['deadline']=$request->deadline;
                    // $this->data[$this->type]['searchDay']=$request->searchDay;
                    // $this->data[$this->type]['searchTime']=$request->searchTime;
                    // $this->data[$this->type]['searchTitle']=$request->searchTitle;
                    $this->data[$this->type]=$tsearchOptions;
                    $userCredit->balance=$userCredit->balance-1;
                    $userCredit->save();
                    // $this->data[$this->type]['user_id']=$request->user_id;
                }
                if($this->checkType('hotels')){
                    // return $request->all();
                    if (isset($request->searchText)) {
                        $q = $request->searchText;
                    }else{
                        $q = "places to stay near me";
                    }
                    $this->type='hotels';
                    $this->params['q']=$q;
                    $this->params['tbm']='lcl';
                    // $this->params['engine']='google_maps';
                    // $this->params['type']='place';
                    // $this->params['data']='!4m5!3m4!1s0x89c259a61c75684f%3A0x79d31adb123348d2!8m2!3d40.7457413!4d-73.98820049999999 ';
                    // $this->rkey='hotels';
                    // $this->rkey='organic_results';
                    $this->rkey='local_results';
                    $requiredFields=['location','latitude','longitude','searchDay','searchTime','searchText'];
                    $optionalFields=[];
                    if($request->searchType=='group'){
                        array_push($requiredFields, 'groupId');
                        array_push($requiredFields, 'deadline');
                    }
                    $res = $this->callAPI($request, $requiredFields, $optionalFields, false);
                    if(!$res){
                        return $res;
                    }
                    // dd($res);
                }
                // unset($this->params['engine']);
                // unset($this->params['type']);
                // unset($this->params['data']);
                if($this->checkType('activities')){
                    if (isset($request->searchText)) {
                        $q = $request->searchText;
                    }else{
                        $q = "things to do near me";
                    }
                    $this->type='activities';
                    $this->params['q']=$q;
                    $this->params['tbm']='lcl';
                    $this->rkey='local_results';
                    $requiredFields=['location','latitude','longitude','searchDay','searchTime','searchText'];
                    $optionalFields=[];
                    if($request->searchType=='group'){
                        array_push($requiredFields, 'groupId');
                        array_push($requiredFields, 'deadline');
                    }
                    $res = $this->callAPI($request, $requiredFields, $optionalFields, false);
                    if(!$res){
                        return $res;
                    }
                }
                if($this->checkType('movie')){
                    $this->data=$this->movie_shows($request);
                }
                if($this->checkType('tv')){
                    $this->data=$this->movie_shows($request);
                }
                if($this->checkType('date_idea')){
                    // $category=; 
                    if(isset($request->searchText)){
                        $dateIdeas = DateIdea::where('category',$request->searchText)->inRandomOrder()->limit(20)->get();
                    }else{
                        $dateIdeas = DateIdea::inRandomOrder()->limit(20)->get();
                    }
                    $this->type='date_idea';
                    $this->data[$this->type] = $dateIdeas;
                    $this->result_count=20;
                }
                if(sizeof($this->data)>0){
                    $data=[];
                    $user = JWTAuth::toUser($request->token);
                    if(!$user){
                        
                    }
                    $resp_data=$this->data;
                    // if($request->searchType=='solo'){
                    //     $data['group_ids']='';
                    // }
                    // if($request->searchType=='group'){
                    //     $data['group_ids']=2;
                    // }
                    $data['searchType']=$request->searchType;
                        $data['location_name']=$request->location;
                        
                        switch ($request->type) {
                            case 'activities':
                                $data['type']='attractions';
                                break;
                            case 'movies_in_theaters':
                                $data['type']='movie';
                                break;
                            default:
                                $data['type']=$request->type;
                                break;
                        }
                        $data['search_title']=$request->searchTitle;
                        $data['search_day']=$request->searchDay;
                        $data['search_time']=$request->searchTime;
                        $data['deadline']=$request->deadline;
                        $data['longitude']=$request->longitude;
                        $data['latitude']=$request->latitude;
                        $data['genres']=$request->genres;
                        $data['network']=$request->serviceAvailability;
                        $data['group_ids']=explode(',', $request->groupId);
                    if($request->searchType!='discover'){
                        $search = $this->searchTransactionRepository->save_search_results($user, $resp_data, $data);
                        $request->search_transaction_id = $search->id;
                        $this->search_transaction_id=$search->id;
                        if(isset($request->skipped_users)){
                            // dd($request->only(['skipped_users', 'search_transaction_id']));
                            $req['skipped_users'] =$request->skipped_users;
                            $req['search_transaction_id'] =$search->id;
                            $res = app()->call('App\Http\Controllers\Api\UserController@add_skip_user',[$request]);
                            // dd($res);
                        }
                        if(!$res){
                            $this->message.=' skipped users not added';
                        }
                    }
                }else{
                    return response()->json(['status'=>'complete','result_count'=>0,'message'=>'No result found']);
                }

                if($this->imagePrefix!=''){
                    $response = [
                        'status' => $this->status,
                        'message' => $this->message,
                        'search_transaction_id' => $this->search_transaction_id,
                        'imagePrefix'=>$this->imagePrefix,
                        'data' => $this->data,
                        'compulsory_likes'=>$this->getCompulsoryLikes(),
                        'result_count'=>$this->result_count,
                    ];
                }else{
                    $response = [
                        'status' => $this->status,
                        'message' => $this->message,
                        'search_transaction_id' => $this->search_transaction_id,
                        'data' => $this->data,
                        'compulsory_likes'=>$this->getCompulsoryLikes(),
                        'result_count'=>$this->result_count,
                    ];
                }
            // } catch (\Exception $e) {
            //     $response = error_reponse_handler($e);
            //     return response()->json($response['response'],$response['status_code']);
            // }
        }
        return response()->json($response,200);
    }
    public function getCompulsoryLikes(){
        // dd($this->result_count);
        if($this->result_count>=10){
            return 5;
        }
        if($this->result_count>=8 && $this->result_count<10){
            return 4;
        }
        if($this->result_count>=6 && $this->result_count<8){
            return 3;
        }
        if($this->result_count>=3 && $this->result_count<6){
            return 2;
        }
        if($this->result_count>=1 && $this->result_count<3){
            return 1;
        }
    }
    public function callAPI($request, $requiredFields, $optionalFields, $requireLogin){
        $res = $this->preparePostData($request, $requiredFields, $optionalFields, $requireLogin);
        if(!$res){
            return $res;
        }
        $client = new Client();
        $res = $client->request($this->method, $this->endpoint, ['query' => $this->params]);
        // dd($res->getBody());
        $this->result_count+=count(json_decode($res->getBody())->{$this->rkey});
        if($this->rkey=='all'){
            $this->data[$this->type] = json_decode($res->getBody())->{$this->rkey};
        }else{
            // if($this->provider==null){
                $this->data[$this->type] = json_decode($res->getBody())->{$this->rkey};
            // }else{
            //     $this->data[$this->type][$this->provider] = json_decode($res->getBody())->{$this->rkey};
            // }
            if($res->getStatusCode()!='200'){
                $this->status=false;
                $this->message='Search not complete';
                $this->statusCode=$res->getStatusCode();
            }    
        }
        
        return true;
    }
    public function checkType($type){
        if(in_array($type, $this->types)){
            return true;
        }else{
            return false;
        }
    }
    public function checkProviders($provider){
        if(in_array($provider, $this->providers)){
            return true;
        }else{
            return false;
        }
    }
    public function movie_shows(Request $request){
        $source = source::all();
        // return json_encode($source);die();
        $response = ['status' => false, 'message' => 'Something Went wrong while processing'];
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            // 'type' => 'required',
        ]);
        if ($validator->fails()) {
            $response =  get_parsed_validation_error_response($validator);
            return response()->json($response, 400);
        }else{
            try{
                $type = $request->input('type');
                $showsarray;
                if($type == 'movie'){
                    $record = DB::table('movies');
                    if ($request->has('genres')) {
                        $array=explode(',',$request->input('genres'));
                        foreach ($array as $key => $value) {
                            $record->orWhereJsonContains('genres', $value);
                        }
                    }
                    if ($request->has('serviceAvailability')){
                        $array=explode(',',$request->input('serviceAvailability'));
                        foreach ($array as $key => $value) {
                            $record->orWhereJsonContains('service_availability', $value);
                        }
                        
                    }
                    // if ($request->has('cast')) {
                    //     $record->whereJsonContains('movie_cast', $request->input('cast'));
                    // }
                    if ($request->has('classification')){
                        $record->where('classification', $request->input('classification'));
                    }
                    $movies = $record->get();
                    $this->result_count+=count($movies);
                    $data['movies']=$movies;
                }
                if($type == 'tv'){
                    $record = DB::table('shows');
                    // if ($request->has('cast')) {
                    //     $record->whereJsonContains('show_cast', $request->input('cast'));
                    // }
                    if ($request->has('classification')){
                        $record->where('classification', $request->input('classification'));
                    }
                    if ($request->has('genres')) {
                        $array=explode(',',$request->input('genres'));
                        foreach ($array as $key => $value) {
                            $record->orWhereJsonContains('genres', $value);
                        }
                    }
                    $shows = $record->get();
                    $this->result_count+=count($shows);
                    $data['tv']=$shows;
                }
                // ->get();
                // if(sizeof($where)>0){
                //     $movies = movie::whereJsonContains('genres', $genres)->get();
                // }else{
                //     $movies = movie::whereJsonContains('genres', $genres)->get();
                // }
                // // print_r($where);
                // die();
                
                // $movies = movie::where('title', 'like', '%'.$title.'%')->whereJsonContains('genres', $genres)->get();
                //
                // $response = [
                //     'status' => true,
                //     'message' => 'success',
                    return $data;
                // ];

            } catch (\Exception $e) {
                $response = error_reponse_handler($e);
                return response()->json($response['response'],$response['status_code']);
            }
        }
        return $response;
        // return response()->json($response,200);
    }
    public function addUserExperience(Request $request){
        $token = $request->token;
        $title = $request->title;
        $dateTime = $request->dateTime;
        $imageName = 'https://unstuq-dev-media.s3.us-east-2.amazonaws.com/'.$request->file('image')->store('experiences', 's3');
        $user = JWTAuth::toUser($request->token);
        $experience_type=$request->experienceType;
        
        if(!$request->experienceId){
            $response = Experience::create([
                'title'=>$request->title,
                'image'=>$imageName,
                'user_id'=>$user->id,
                'name'=>$user->name,
                'choices'=>json_encode($request->selectedItems),
                'experience_date_time'=>$request->dateTime
            ]);
            $experience_id=$response->experience_id;
        }else{
            $experience_id = $request->experienceId;
            $response = Experience::where('experience_id',$request->experienceId)->first();
        }

        
        $usr_experience=true;
        if($experience_type=='group'){
            $user_ids=$request->userIds;
            $user_ids_array = explode(',', $user_ids);
            foreach ($user_ids_array as $user_id ){
                $invited_user=User::where('id',$user_id)->first();
                $usr_experiencet = UserExperience::create([
                    'experience_id'=>$experience_id,
                    'user_id'=>$user_id,
                ]);
                if(!$usr_experiencet){
                    $usr_experience=false;
                }else{
                    $user_device = $this->userDeviceRepository->findOneFromArray([
                        'user_id' => $user_id
                    ]);
                    
                    $msg_data = [
                        'invited_user_name' => explode(" ",$invited_user->name)[0],
                        'user_invited_by' => $user->name,
                        'experience_title' => $response->title,
                        // 'user_phone_no' => $data['user_phone_no'],
                    ];
                    
                    if(!empty($user_device["player_id"]))
                    {
                        //send push notification
                        $message_name = 'experience_add_notification_message';
                        $msg = get_message_text($message_name, $msg_data);
                        sendMessage($user_device["player_id"],$msg,$type = "experience");  
                    }
                }
            }
        }
        if($usr_experience && $response){
            $response = [
                'message'=>'User experience added',
                'status' => true,
                'data' => $usr_experience,
            ];
        }else{
            $response = [
                'message'=>'Something went wrong',
                'status' => false,
                'data' => [],
            ];
        }
        return response()->json($response);
    }
    public function getUserExperience(Request $request){
        $user = JWTAuth::toUser($request->token);
        $userExperience['my_experiences'] = Experience::where('user_id',$user->id)->get();
        $userExperience['invited_experiences'] = DB::table('experiences')
        ->leftJoin('user_experiences','user_experiences.experience_id','=','experiences.experience_id')
        ->where('user_experiences.user_id',$user->id)
        ->get();
        if($userExperience){
            $response = [
                'message'=>"Found User experience",
                'count'=>count($userExperience['my_experiences'])+count($userExperience['invited_experiences']),
                'status' => true,
                'data' => $userExperience,
            ];
        }else{
            $response = [
                'message'=>'Something went wrong',
                'status' => false,
                'data' => [],
            ];
        }
        return response()->json($response);
    }
    public function leave_delete_experience(Request $request){
        $experience_id = $request->experienceId;
        $user = JWTAuth::toUser($request->token);
        $user_id = $user->id;
        $type = $request->type;
        $isRemoved = false;
        if($type=='delete'){
            $experience_members = UserExperience::where('experience_id',$experience_id)->get();
            $experience_creator = Experience::where('experience_id',$experience_id)->with('user')->first();
            
            // return $experience_creator;
            // return $experience_creator->user->name;
            $res = UserExperience::where('experience_id',$experience_id)->delete();
            $res1 = Experience::where('experience_id',$experience_id)->delete();
            if($res && $res1){
                $isRemoved = true;
                $msg="Experience Deleted";
                foreach ($experience_members as $experience_member) {
                    $user_device = $this->userDeviceRepository->findOneFromArray([
                        'user_id' => $experience_member->user_id
                    ]);
                    if(!empty($user_device["player_id"])){
                        //send push notification
                        $msg = explode(' ', $experience_creator->name)[0]." just canceled their experience '".$experience_creator->title."' scheduled for ".date("F jS, Y", strtotime($experience_creator->experience_date_time));
                        sendMessage($user_device["player_id"],$msg,$type = "delete_experience");  
                    }
                }
            }
        }else{
            $res = UserExperience::where('experience_id',$experience_id)->where('user_id',$user_id)->delete();
            // return $res;
            if($res){
                $res1 = Experience::where('experience_id',$experience_id)->first();
                
                //return $res1;
                if(!$res1){
                    $response = [
                        'message'=>'Experience not found',
                        'status' => false,
                        'data' => [],
                    ];
                    return response()->json($response);
                }
                else
                {
                    // dd($res1->user_id);
                    $isRemoved = true;
                    $msg="Experience Removed";
                    $user_device = $this->userDeviceRepository->findOneFromArray([
                        'user_id' => $res1->user_id
                    ]);
                    
                    if(!empty($user_device["player_id"]))
                    {
                        //send push notification
                        $message_name = 'experience_add_notification_message';
                        $msg = $user->name." just left your experience '".$res1->title."' scheduled for ".date("F jS, Y", strtotime($res1->experience_date_time));
                        sendMessage($user_device["player_id"],$msg,$type = "leave_experience");  
                    }
                }
            }
        }
        if($isRemoved){
            $response = [
                'message'=>$msg,
                'status' => true,
                'data' => [],
            ];
        }else{
            $response = [
                'message'=>"Something went wrong",
                'status' => false,
                'data' => [],
            ];
        }
        return response()->json($response);
    }
    // not in use old code
    // public function addExperiences(Request $request){
    //     $response = ['status' => false, 'message' => 'Something Went wrong while processing'];
    //     $user = JWTAuth::toUser($request->token);
    //     $validator = Validator::make($request->all(), [
    //         'title' => 'required',
    //         'image' => 'required',
    //         'userIds' => 'required',
    //         'experienceDateTime' => 'required',
    //         'choices' => 'required',
            
    //     ]);
    //     if ($validator->fails()) {
    //         $response =  get_parsed_validation_error_response($validator);
    //         return response()->json($response, 400);
    //     }else{
    //         try{
    //             if ($request->hasFile('image')) {
    //                 if($request->file('image')->isValid()) {
    //                     $file = $request->file('image');
    //                     $name = 'experience_'.rand(11111, 99999) . '.' . $file->getClientOriginalExtension();
    //                     $request->file('image')->move(public_path('/uploads/experiences'), $name);
    //                 }
    //             }
    //             if(!file_exists(public_path('/uploads/experiences/'.$name))){
    //                 $name='Avtar.jpg';
    //             }

    //             $experience = new Experience;
    //             $experience->title=$request->title;
    //             $experience->image=$name;
    //             $experience->user_id=$user->id;
    //             $experience->experience_date_time=$request->experienceDateTime;
    //             $experience->choices=$request->choices;
    //             $data=$experience->save();
    //             // dd($experience->experience_id);
    //             $data1=true;
    //             if($data){
    //                 $userIds = explode(',', $request->userIds);
    //                 foreach ($userIds as $userKey => $userValue) {
    //                     // echo 'userId '.$userValue;
    //                     $userExperience = new UserExperience;
    //                     $userExperience->experience_id=$experience->experience_id;
    //                     $userExperience->user_id=$userValue;
    //                     $usr=User::where('user_id',$user_id)->first();
    //                     array_push($this->deviceId, $usr->fcm_token);
    //                     $status=$userExperience->save();
    //                 }
    //             }
    //             //shared_experience_message
    //             $msg_data = [
    //                 'invited_user_name' => $user->name,
    //                 'user_invited_by' => $user->name,
    //                 'user_phone_no' => $user->phone,
    //             ];
    //             $message_name = 'shared_experience_message';
    //             $msg = get_message_text($message_name, $msg_data);
    //             $response = sendMessage($this->deviceId, $msg,'experience');
    //             $response = [
    //                 'status' => $status,
    //                 'message' => 'success',
    //                 'data' => $data,
    //             ];
    //         }catch (\Exception $e) {
    //             $response = error_reponse_handler($e);
    //             return response()->json($response['response'],$response['status_code']);
    //         }
    //         return response()->json($response,200);
    //     }
    // }
    // public function viewExperiences(Request $request){
    //     $response = ['status' => false, 'message' => 'Something Went wrong while processing'];
    //     $validator = Validator::make($request->all(), [
    //         'token' => 'required',
    //         'userId' => 'required',
    //     ]);
    //     if ($validator->fails()) {
    //         $response =  get_parsed_validation_error_response($validator);
    //         return response()->json($response, 400);
    //     }else{
    //         try{
    //             $experience = Experience::where('user_id',$request->userId)->get();
    //             $shared = UserExperience::where('user_id',$request->userId)->get();
    //             $data['created']=$experience;
    //             $data['shared']=$shared;
    //             $response = [
    //                 'status' => true,
    //                 'message' => 'success',
    //                 'data' => $data,
    //             ];
    //         }catch (\Exception $e) {
    //             $response = error_reponse_handler($e);
    //             return response()->json($response['response'],$response['status_code']);
    //         }
    //         return response()->json($response,200);
    //     }
    // }
    // public function shareExperiences(Request $request){
    //     $response = ['status' => false, 'message' => 'Something Went wrong while processing'];
    //     $validator = Validator::make($request->all(), [
    //         'experienceId' => 'required',
    //         'userId' => 'required',
    //     ]);
    //     if ($validator->fails()) {
    //         $response =  get_parsed_validation_error_response($validator);
    //         return response()->json($response, 400);
    //     }else{
    //         try{
    //             $userId = explode(',', $request->userId);
    //             foreach ($userId as $userKey => $userValue) {
    //                 $userExperience = new UserExperience;
    //                 $userExperience->experience_id=$request->experienceId;
    //                 $userExperience->user_id=$userValue;
    //                 $this->status=$userExperience->save();
    //             }
    //             $response = [
    //                 'status' => $this->status,
    //                 'message' => 'Success',
    //                 'data' => [],
    //             ];
    //         }catch (\Exception $e) {
    //             $response = error_reponse_handler($e);
    //             return response()->json($response['response'],$response['status_code']);
    //         }
    //         return response()->json($response,200);
    //     }
    // }
    // public function sharedExperiences(Request $request){
    //     $response = ['status' => false, 'message' => 'Something Went wrong while processing'];
    //     $validator = Validator::make($request->all(), [
    //         'token' => 'required',
    //         'userId' => 'required',
    //     ]);
    //     if ($validator->fails()) {
    //         $response =  get_parsed_validation_error_response($validator);
    //         return response()->json($response, 400);
    //     }else{
    //         try{
    //             $sharedExperiences = UserExperience::where('user_id',$request->userId)->get();
    //             $response = [
    //                 'status' => true,
    //                 'message' => 'Success',
    //                 'data' => $sharedExperiences,
    //             ];
    //         }catch (\Exception $e) {
    //             $response = error_reponse_handler($e);
    //             return response()->json($response['response'],$response['status_code']);
    //         }
    //         return response()->json($response,200);
    //     }
    // }
    // public function leaveOrDeleteExperience(Request $request){
    //     $user = JWTAuth::toUser($request->token);
    //     $user_id = $user->id;
    //     $experience_id=$request->experienceId;
    //     $type=$request->type;
    //     if($type=='shared'){
    //         $userExperience=UserExperience::where('experience_id',$experience_id)->where('user_id',$user_id)->first()->delete();
    //         if($userExperience){
    //             $status=true;
    //             $message='Shared experience removed';
    //         }
    //     }else{
    //         $experience=Experience::where('experience_id',$experience_id)->first()->delete();
    //         $userExperience=UserExperience::where('experience_id',$experience_id)->where('user_id',$user_id)->first()->delete();
    //         if($experience && $userExperience){
    //             $status=true;
    //             $message='Experience removed';
    //         }
    //     }
    //     $response =[
    //         'status'=>$status,
    //         'message'=>$message
    //     ];
    //     return response()->json($response,200);
    // }
    public function userSources(Request $request){
        try{
            $this->user = JWTAuth::toUser($request->token);
            if(!$this->user){
                return response()->json(['message'=>'User not found',400]);
            }
            $sources = source::with(['UserSource' => function($q){
                $q->where('user_id', $this->user->id);
            }])->get();
            return response()->json($sources, 200);
        }catch(\Exception $e) {
            $response = error_reponse_handler($e);
            return response()->json($response['response'],$response['status_code']);
        }
    }
    public function addUserSources(Request $request){
        try{
            $result=true;
            $noChange=true;
            $validator = Validator::make($request->all(), [
                'sourceIds' => 'required|string',
            ]);
            if ($validator->fails()) {
                $response =  get_parsed_validation_error_response($validator);
                return response()->json($response, 400);
            }else{
                $user = JWTAuth::toUser($request->token);
                if(!$user){
                    return response()->json(['message'=>'User not found',400]);
                }
                $userId = $user->id;
                $sourceIds = explode(',', $request->sourceIds);
                foreach($sourceIds as $key => $sourceId){
                    // echo " sourceId ".$sourceId." <br>";
                    $isUserSource = UserSource::where('user_id',$userId)->where('source_id',$sourceId)->first();
                    if(!$isUserSource){
                        $userSource = UserSource::create([
                            'user_id'=>$userId,
                            'source_id'=>$sourceId
                        ]);
                        $noChange=false;
                        if(!$userSource){
                            $result = false;
                        }
                    }
                }
            }
            if($result && !$noChange){
                $message="User Source added successfully";
                $status=true;
            }else{
                if($noChange){
                    $message="No change in data Please try to add new sources.";
                }else{
                    $message="Something went wrong.";
                }
                $status=false;
            }
            $response = Array(['message'=>$message,'status_code'=>$status]);
            return response()->json($response, 200);
            // return json_encode($sources);
        }catch(\Exception $e) {
            $response = error_reponse_handler($e);
            return response()->json($response['response'],$response['status_code']);
        }
    }
    public function uploadImage(Request $request){
        $this->validate($request, ['image' => 'required|image']);
        $fileKey='image';
        $filePath = 'profile_images';
        $bucket='s3';
        // if($request->hasfile($fileKey)){
        //     $file = $request->file($fileKey);
        //     $name=time().$file->getClientOriginalName();
        //     $ar = Storage::disk($bucket)->put($filePath.$name, file_get_contents($file));
        //     $imgUrl=Storage::disk($bucket)->url($name);
        //     return response()->json([
        //         'status'=>$ar,
        //         'message'=>'Image Uploaded successfully',
        //         'imgUrl'=>$imgUrl
        //     ]);
        // }else{
        //     return response()->json([
        //         'status'=>false,
        //         'message'=>$fileKey.' is missing',
        //     ]);
        // }
        // return json_encode(AmazonFileHelper::UploadFile($request,$fileKey,$filePath,$bucket));
        $documentPath = 'https://unstuq-dev-media.s3.us-east-2.amazonaws.com/'.$request->file('image')->store('profile_images', 's3');
        return $documentPath;
        //profile_images/lwryuiS0DX0srb24tqba4qjpS6CwLdjlPeDGx20z.jpg
    }
}