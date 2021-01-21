<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\SearchTransaction\SearchTransactionRepositoryInterface;
use App\Repositories\SearchTransactionGroup\SearchTransactionGroupRepositoryInterface;
use App\Repositories\SearchTransactionUser\SearchTransactionUserRepositoryInterface;
use App\Repositories\MatchMaker\MatchMakerRepositoryInterface;

class UpdateLiveSearch extends Command
{
    private $searchTransactionRepository;
    private $searchTransactionGroupRepository;
    private $searchTransactionUserRepository;
    private $matchMakerRepository;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'live:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'check live search for expiry';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(SearchTransactionRepositoryInterface $searchTransactionRepository,
        SearchTransactionGroupRepositoryInterface $searchTransactionGroupRepository,
        SearchTransactionUserRepositoryInterface $searchTransactionUserRepository,
        MatchMakerRepositoryInterface $matchMakerRepository)
    {
        parent::__construct();
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
        $x_hours = ( 24 * 60 * 60 );
        $add_x_hour_after = strtotime(gmdate("Y-m-d H:i:s"))  -  $x_hours;
        $check = gmdate("Y-m-d H:i:s",$add_x_hour_after);

        //hide live search if meet time passed 24 hours
        $searches = $this->searchTransactionRepository->findMultipleFromArray([
            ['live', '=', '1'],
            ['meet_time', '>=',$check ],
        ]);

        foreach($searches as $search){
            $this->searchTransactionRepository->update([
                        'live' => 0
                    ],$search->id);
        }
    }
}
