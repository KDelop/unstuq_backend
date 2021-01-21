<?php

namespace App\Repositories\UserFavorite;

use App\Models\UserFavorite;
use App\Repositories\UserFavorite\UserFavoriteRepositoryInterface;

use App\Repositories\Contracts\RepositoryInterface;
use App\Repositories\Eloquent\Repository;
use Illuminate\Support\Facades\DB;

class UserFavoriteRepository extends Repository implements UserFavoriteRepositoryInterface
{

    public function __construct(UserFavorite $model)
    {
        parent::__construct($model);
    }

    public function remove($data){
        $this->model->where($data)->delete();
    }

    public function get_favorites($user_id, $type,$longitude,$latitude){
		$businesses = $this->distance_sql($latitude, $longitude);
		return  UserFavorite::join('businesses', 'businesses.location_id', '=', 'user_favorites.entity_id')
            ->where("user_favorites.user_id",$user_id)
            ->where("user_favorites.type",$type)
            ->whereIn('businesses.location_id', $businesses)
            ->get();
    }

    public function remove_fav($user_id,$entity_ids, $type){
        if($type == 4 || $type == 5){
            return $this->model->where('user_id','=',$user_id)->whereIn('entity_id',$entity_ids)->whereIn('type',[4,5])->delete();
        }
        else{
            return $this->model->where('user_id','=',$user_id)->whereIn('entity_id',$entity_ids)->where('type',$type)->delete();
        }
    }
	
	public function distance_sql($lat,$lng){
		$output = DB::select("select distinct location_id, ( 3959 * acos( cos( radians( $lat ) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians( {$lng} ) ) + sin( radians( $lat ) ) * sin( radians( `latitude` ) ) ) ) AS distance FROM `businesses` HAVING distance <= 15 ORDER BY distance");
		
		$result = array_column(json_decode(json_encode($output),true), 'location_id');
		return $result;
	}
}
