<?php

use Illuminate\Database\Seeder;
use App\Models\MatchMaker;
use App\Models\SearchTransaction;
use App\Models\UserFavorite;

class MatchSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        MatchMaker::truncate();
        UserFavorite::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        //auto fill user response with random like and dislike
        $searches = SearchTransaction::where('status', '0')->get();

        foreach( $searches as $search ){
            //get all users with search
            $groups = $search->groups;
            $unique_members = [];

            foreach($groups as $group){
                $members = $group->members;
                foreach($members as $member){
                    $unique_members[$member->id] = $member;
                }
            }

            $results = json_decode($search->results);
            $entities = $results->results;

            foreach($unique_members as $member){

               foreach( $entities as $entity){
                   $like_dislike = rand(0,1);
                    MatchMaker::create([
                        'user_id' => $member->id, 
                        'search_transaction_id' => $search->id,
                        'entity_id' => $entity->location_id,
                        'like_dislike' =>  $like_dislike,
                        'created_at' => gmdate('Y-m-d H:i:s')
                    ]);

                    if( $like_dislike == 1){
                        // add to favorite
                    }
               }
            }
        }
    }
}
