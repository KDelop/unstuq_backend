<?php

namespace App\Console\Commands;
use App\Models\SearchTransaction;
use Illuminate\Console\Command;
use App\Repositories\SearchTransaction\SearchTransactionRepositoryInterface;
use App\Repositories\SearchTransactionGroup\SearchTransactionGroupRepositoryInterface;
use App\Repositories\SearchTransactionUser\SearchTransactionUserRepositoryInterface;
use App\Repositories\MatchMaker\MatchMakerRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\UserDevice\UserDeviceRepositoryInterface;


class NotifyPending extends Command
{
    private $searchTransactionRepository;
    private $searchTransactionGroupRepository;
    private $searchTransactionUserRepository;
    private $matchMakerRepository;
    private $userRepository;
    private $userDeviceRepository;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pending_search:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check any pending search';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(SearchTransaction $model,
        UserRepositoryInterface $userRepository,
        UserDeviceRepositoryInterface $userDeviceRepository,
        SearchTransactionRepositoryInterface $searchTransactionRepository,
        SearchTransactionGroupRepositoryInterface $searchTransactionGroupRepository,
        SearchTransactionUserRepositoryInterface $searchTransactionUserRepository,
        MatchMakerRepositoryInterface $matchMakerRepository)
    {
        parent::__construct();
        $this->model = $model;
        $this->userRepository = $userRepository;
        $this->userDeviceRepository = $userDeviceRepository;
        $this->searchTransactionRepository = $searchTransactionRepository;
        $this->searchTransactionGroupRepository = $searchTransactionGroupRepository;
        $this->searchTransactionUserRepository = $searchTransactionUserRepository;
        $this->matchMakerRepository = $matchMakerRepository;
    }


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //notify users x hours before deadline to submit there responses
        $x_hours = ( 1 * 60 * 60 );
        $add_x_hour_after = strtotime(gmdate("Y-m-d H:i:s"))  + $x_hours;
        $check = gmdate("Y-m-d H:i:s",$add_x_hour_after);
        $pending_deadline = date("Y-m-d");

        $searches = $this->model->where('status','0')->whereDate('deadline','>=',$pending_deadline)->get();
        foreach($searches as $search){

           //$groups = $search->groups;
           $unique_members = [];

           //get any user who are not avialable or skipped the event
           $skippedUsers = $this->searchTransactionUserRepository->findMultipleFromArray([
               'search_transaction_id' => $search->id
           ])->toArray();
           $unique_members = get_search_users_attending_event($search, $skippedUsers);

            if(!empty($unique_members)){
               foreach($unique_members as $user){
                    $member_exists = $this->userRepository->findOneFromArray([
                                    'phone' => $user['phone']
                                ]);
                    if( $member_exists ){
                        $member = $member_exists;
                        $user_id = $member_exists->id;
                        $user_device = $this->userDeviceRepository->findOneFromArray([
                                    'user_id' => $user['id']
                                ]);
                        $msg_data = [
                            'invited_user_name' => explode(" ",$member->name)[0],
                            'user_invited_by' => $user['name'],
                            'user_phone_no' => $user['phone'],
                        ];
                        $message_name = 'notify_pending_message';
                        $msg = get_message_text($message_name, $msg_data);

                        if(!empty($user_device['player_id'])){
                            //send push notification
                            sendMessage($user_device['player_id'],$msg,$type = "pending");
                        }
                        else
                        {
                            //send sms
                            twilio_send_sms($msg, $user['phone']);
                        }
                    }else{
                        //send text message invite to join app
                        $msg_data = [
                            'invited_user_name' => explode(" ",$user['name'])[0],
                            'user_invited_by' => $user->name,
                            'user_phone_no' => $user['phone'],
                        ];
                        $message_name = 'notify_pending_text_message';
                        $msg = get_message_text($message_name, $msg_data);
                        //send sms
                        twilio_send_sms($msg, $user['phone']);
                    }
                }

            }

            //update flag as notified
            $this->searchTransactionRepository->update([
                    'pending_notification' => 1
                ],$search->id);


        }

    }
}
