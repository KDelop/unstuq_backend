<?php

namespace App\Repositories\MatchMaker;

use App\Models\MatchMaker;
use App\Repositories\MatchMaker\MatchMakerRepositoryInterface;

use App\Repositories\Contracts\RepositoryInterface;
use App\Repositories\Eloquent\Repository;
use Illuminate\Support\Facades\DB;

class MatchMakerRepository extends Repository implements MatchMakerRepositoryInterface
{
    public function __construct(MatchMaker $model)
    {
        parent::__construct($model);
    }

    public function get_liked_entity_count($search_id){

        return  MatchMaker::select([DB::raw('sum(like_dislike) as count'),'search_transaction_id','entity_id' ])
                    ->having("search_transaction_id",$search_id)
                    ->having("count",'>',1)
                    ->groupBy("entity_id","search_transaction_id")
                    ->orderBy("count",'desc')
                    ->first();
    }

    public function checkUsersSubmitted($search_id){
        return  MatchMaker::select([DB::raw('count(*) as count') ])
        ->having("search_transaction_id",$search_id)
        ->groupBy("user_id","search_transaction_id")->get();
    }

    public function get_first_liked_entity($search_id,$search_user_id){

        return  MatchMaker::select('search_transaction_id','entity_id' )
            ->where("search_transaction_id",$search_id)
            ->orderBy('created_at','DESC')
            //->where("user_id",$search_user_id)
            ->first();
    }


    public function get_like_dislike_status($user_id,$search_id) {
        $record = MatchMaker::where("search_transaction_id",$search_id)
            ->where("user_id",$user_id)
            ->first();

        if(empty($record))
        {
            return false;
        } else {
            return true;
        }
    }
}
