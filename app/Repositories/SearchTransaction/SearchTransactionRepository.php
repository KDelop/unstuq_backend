<?php

namespace App\Repositories\SearchTransaction;

use App\Models\SearchTransaction;
use App\Models\MatchMaker;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\UserGroup\UserGroupRepositoryInterface;
use App\Repositories\SearchTransaction\SearchTransactionRepositoryInterface;
use App\Repositories\SearchTransactionGroup\SearchTransactionGroupRepositoryInterface;
use App\Repositories\SearchTransactionUser\SearchTransactionUserRepositoryInterface;
use App\Repositories\MatchMaker\MatchMakerRepositoryInterface;
use App\Repositories\UserDevice\UserDeviceRepositoryInterface;
use App\Repositories\Contracts\RepositoryInterface;
use App\Repositories\Eloquent\Repository;
use phpDocumentor\Reflection\Types\Null_;
use DB;
class SearchTransactionRepository extends Repository implements SearchTransactionRepositoryInterface
{
    private $userRepository;
    private $userGroupRepository;
    private $searchTransactionGroupRepository;
    private $searchTransactionUserRepository;
    private $matchMakerRepository;
	private $userDeviceRepository;
    
    public function __construct(SearchTransaction $model,
        UserRepositoryInterface $userRepository,
        UserGroupRepositoryInterface $userGroupRepository,
        SearchTransactionGroupRepositoryInterface $searchTransactionGroupRepository,
        SearchTransactionUserRepositoryInterface $searchTransactionUserRepository,
        MatchMakerRepositoryInterface $matchMakerRepository,
		UserDeviceRepositoryInterface $userDeviceRepository)
    {
        parent::__construct($model);
        $this->userRepository = $userRepository;
        $this->userGroupRepository = $userGroupRepository;
        $this->searchTransactionGroupRepository = $searchTransactionGroupRepository;
        $this->searchTransactionUserRepository = $searchTransactionUserRepository;
        $this->matchMakerRepository = $matchMakerRepository;
		$this->userDeviceRepository = $userDeviceRepository;
    }

    public function save_search_results($user, $result_array, $data){

        if($data['searchType']=='solo'){
            $search_user_type = 1; //'solo'
        }
        if($data['searchType']=='group'){
            $search_user_type = 2; //'group'
        }

        if(!isset($data['location_name'])){
            $data['location_name'] = "";
        }
        
        $search_types = config('constant.search_event_types_reverse');
        $search_type_id = $search_types[$data['type']];

        $search = $this->create([
            'user_id' => $user->id,
            'search_title' => $data['search_title'],
            'search_type' => $search_type_id,
            // 'activity_type' => $data['activity_type'],
            'search_user_type' => $search_user_type,
            'meet_time' => $data['search_day']." ".$data['search_time'].":00",
            'deadline' => isset($data['deadline']) ? $data['deadline']: '',
            'location_name' =>  isset($data['location_name']) ? $data['location_name']: '',
            'location_longitude' =>  isset($data['longitude']) ? $data['longitude']:'',
            'location_latitude' =>  isset($data['latitude']) ? $data['latitude'] : '',

            'genre' =>  isset($data['genres']) ? $data['genres']: null,
            'network' =>  isset($data['network']) ? $data['network'] : null,

            'results' => json_encode($result_array),
            'created_at' => (string)gmdate('Y-m-d H:i:s'),
            'search_parameters'=>json_encode($data)
        ]);


        //add groups
        // dd($data['group_ids']);
        if(!empty($data['group_ids'])){
            foreach($data['group_ids'] as $group_id){
                //check if group id exists
                if($user->groups->contains($group_id)){
                   $search->groups()->attach($group_id);
                }
            }

            //send notification to all users with search id
            $unique_members = [];
            foreach($data['group_ids'] as $group_id){
                $group = $this->userGroupRepository->find($group_id);
                $members = $group->members;
                foreach($members as $member){
                    $unique_members[$member->id] = $member;
                }
            }
			
			foreach($unique_members as $member){
                $thisMember =  json_decode(json_encode($member));
				if($thisMember->status == "active"){
					
					if($thisMember->id != $user->id)
					{
						$user_id = $thisMember->id;
						$user_device = $this->userDeviceRepository->findOneFromArray([
							'user_id' => $thisMember->id
						]);
						$msg_data = [
							'invited_user_name' => explode(" ",$member->name)[0],
							'user_invited_by' => $user->name,
						];
						
						$message_name = 'search_invite_message_push';
						$msg = get_message_text($message_name, $msg_data);

						if(!empty($user_device['player_id'])){
							//send push notification
							sendMessage($user_device['player_id'],$msg,$type = "pending");
						}
						else
						{
							//send sms
							twilio_send_sms($msg, $thisMember->phone);
						}	
					}

                 }else{
                    //send sms msg
					$msg_data = [
						'invited_user_name' => explode(" ",$member->name)[0],
						'user_invited_by' => $user->name,
					];
					$message_name = 'search_invite_message';
					$msg = get_message_text($message_name, $msg_data);
					twilio_send_sms($msg, $thisMember->phone);
                 }
            }
        }

        return $search;

    }
    public function remove_search($search_id){
        return $this->model->where('id',$search_id)->delete();
    }

    public function getVotes($search_id){
        //SELECT entity_id, SUM(like_dislike) as votes FROM `match_makers` WHERE search_transaction_id = 99 GROUP BY entity_id
        // MatchMaker::selectRaw('entity_id, SUM(like_dislike) as votes');
        // MatchMaker::where('search_transaction_id',$search_id);

        // return MatchMaker::select(DB::select(`entity_id`)
        // ->addSelect(DB::raw(`sum(like_dislike) as votes`))
        // ->from(`match_makers`)
        // ->where(`search_transaction_id`, `=`, 99)
        // ->groupBy(`entity_id`)
        // ->get();

        return MatchMaker::select([DB::raw('sum(like_dislike) as votes'),'search_transaction_id','entity_id' ])
        ->having("search_transaction_id",$search_id)
        // ->having("count",'>',1)
        ->groupBy("entity_id","search_transaction_id")
        // ->orderBy("count",'desc')
        ->get();
    }
  
    public function get_pending_matched_crew_data($status,$check_date,$user_ids){
        $now = date("Y-m-d");
        $pending_deadline = date('Y-m-d H:i:s', strtotime($now . ' -1 day'));
        $solo_records = '';
        $group_records = '';
        if($status == 'pending'){

           return $this->model->whereIn('user_id',$user_ids)->whereDate('deadline','>=',$pending_deadline)->where('status',0)->get();


        }else{
            return $this->model->whereIn('user_id',$user_ids)->where('created_at','>=',$check_date)->where('status',1)->get();
        }
        
     }
	
	public function get_pending_data($grps_arr,$status,$check_date,$user_id){
        $now = date("Y-m-d");
        $pending_deadline = date('Y-m-d H:i:s', strtotime($now . ' -1 day'));
        $solo_records = '';
        $group_records = '';
        if($status == 'pending'){
            // for solo
            $solo_records = $this->model->where('matched_entity_id','!=',0)->where('search_user_type',1)->where('user_id',$user_id)->whereDate('deadline','>=',$pending_deadline)->where('status',0)->get();
            // $solo_records->addSelect(DB::raw("'0' as votes"));
            // dd($solo_records[0]->votes);
            // for group
           // $group_records =  $this->model->select('name','surname')->where('search_user_type',2)->whereHas('groups', function($query) use ($grps_arr) {

           $group_records =  $this->model->where('search_user_type',2)->whereHas('groups', function($query) use ($grps_arr) {
                $query->whereIn('user_group_id',$grps_arr);
            })->whereDate('deadline','>=',$pending_deadline)->where('status',0)->get();
           // $group_records->addSelect(DB::raw("'0' as votes"));
           // dd($group_records[0]);

        }else{
            // for solo
            $solo_records = $this->model->where('search_user_type',1)->where('user_id',$user_id)->where('created_at','>=',$check_date)->where('status',1)->get();

            // for group
            $group_records = $this->model->where('search_user_type',2)->whereHas('groups', function($query) use ($grps_arr) {
                $query->whereIn('user_group_id',$grps_arr);
            })->where('created_at','>=',$check_date)->where('status',1)->get();
        }
        // dd($group_records[0]->votes);
        // combine solo & group requests
        $result = $solo_records->merge($group_records);
        return $group_records;

    }

}