<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\SearchTransaction\SearchTransactionRepositoryInterface;
use App\Repositories\SearchTransactionGroup\SearchTransactionGroupRepositoryInterface;
use App\Repositories\SearchTransactionUser\SearchTransactionUserRepositoryInterface;
use App\Repositories\MatchMaker\MatchMakerRepositoryInterface;
use App\Repositories\UserDevice\UserDeviceRepositoryInterface;

class MatchMaker extends Command
{
    private $searchTransactionRepository;
    private $searchTransactionGroupRepository;
    private $searchTransactionUserRepository;
    private $matchMakerRepository;
    private $userDeviceRepository;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'match:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check matcheds search results';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(SearchTransactionRepositoryInterface $searchTransactionRepository,
            SearchTransactionGroupRepositoryInterface $searchTransactionGroupRepository,
            SearchTransactionUserRepositoryInterface $searchTransactionUserRepository,
            UserDeviceRepositoryInterface $userDeviceRepository,
            MatchMakerRepositoryInterface $matchMakerRepository)
    {
        parent::__construct();
        $this->searchTransactionRepository = $searchTransactionRepository;
        $this->searchTransactionGroupRepository = $searchTransactionGroupRepository;
        $this->searchTransactionUserRepository = $searchTransactionUserRepository;
        $this->matchMakerRepository = $matchMakerRepository;
        $this->userDeviceRepository = $userDeviceRepository;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Running match making algorithm');
        //60 * 60 =3600 + 5*60 = 3900
        $cron_interval = 5; //in mins
        $x_hour_before = 0; //in hours

        //will check if deadline is in next 5 mins then run match making
        $x_hours = ( $x_hour_before * 60 * 60 ) + ( $cron_interval * 60 );
        $add_x_hour_after = strtotime(gmdate("Y-m-d H:i:s"))  +  $x_hours;
        $check = gmdate("Y-m-d H:i:s",$add_x_hour_after);
        $this->info('current time = '. $check);

        //get all pending search transction with deadline less than 1 hour
        $searches = $this->searchTransactionRepository->findMultipleFromArray([
            ['status', '=', '0'],
            ['deadline', '<=',$check ],
        ]);
        $check = [];
        foreach( $searches as $search ){
            //get all users response
            $check[$search->id] = [];

            //check if all attending users submitted
            $count_submitted_users = $this->matchMakerRepository->checkUsersSubmitted([
                'search_transaction_id' => $search->id
            ])->toArray();

            //get any user who are not avialable or skipped the event
            $skippedUsers = $this->searchTransactionUserRepository->findMultipleFromArray([
                'search_transaction_id' => $search->id
            ])->toArray();

            $unique_members = get_search_users_attending_event($search, $skippedUsers );
            if( count($count_submitted_users) > 0 ) {
                if (count($unique_members) == count($count_submitted_users)) {
                    $max_liked_entity = $this->matchMakerRepository->get_liked_entity_count($search->id);
                    //if no match found use first location liked by user search -- pending to do
                    if(empty($max_liked_entity)){
                        $first_liked_entity = $this->matchMakerRepository->get_first_liked_entity($search->id,$search->user_id);
                        $max_liked_entity = $first_liked_entity;
                        $max_liked_entity['count'] =1;
                    }


                    $this->info('count '.$max_liked_entity['count'].'match found entity id - '.$max_liked_entity['entity_id']);

                    //get reviews for matched entity
                    $location_reviews = '';
                    $host = env('SEARCH_API_HOST');
                    $key = env('SEARCH_API_KEY');
                    $search_type_name = config('constant.search_event_types')[$search->search_type];
                    $url = env('SEARCH_API_URL')."/".$search_type_name."/get-details";
                    $data['api_key'] =  env('API_KEY');
                    $data['location_id'] = $max_liked_entity['entity_id'];
                    $data['currency'] = 'USD';
                    $data['lang'] = "en-US";
                    $headers = [ 'Content-Type: application/json',
                        'x-rapidapi-host: '.$host,
                        'x-rapidapi-key: '.$key ];
                    $method = "GET";
                    $location_details = callAPI($method, $url, $data , $headers);
                    if($location_details){
                        if(property_exists($location_details,'reviews')){
                            $location_reviews = $location_details->reviews;
                        }
                    }

                    //update macthed entity
                    $this->searchTransactionRepository->update([
                        'matched_entity_id' => $max_liked_entity['entity_id'],
                        'matched_entity_reviews' => $location_reviews,
                        'status' => 1
                    ],$search->id);

                    foreach($unique_members as $user){
                        //notify all users with matched search results
                        $msg_data = [
                            'location_id' => $max_liked_entity['entity_id'],
                            'location_name' => isset($location_details->name) ? $location_details->name :'',
                            'location_address' => isset($location_details->address) ? $location_details->address :'',
                        ];
                        $user_device = $this->userDeviceRepository->findOneFromArray([
                            'user_id' => $user['id']
                        ]);
                        $type = 'match';
                        $message_name = 'search_full_match_message';
                        $msg = get_message_text($message_name, $msg_data);
                        sendMessage($user_device["player_id"],$msg,$type);
                    }
                }
            }


        }

        $this->info('Completed checking all search transactions pending');
    }
}
