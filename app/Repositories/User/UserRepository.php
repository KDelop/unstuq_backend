<?php

namespace App\Repositories\User;

use App\Models\User;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\Contracts\RepositoryInterface;
use App\Repositories\Eloquent\Repository;
// use Illuminate\Support\Collection;

class UserRepository extends Repository implements UserRepositoryInterface
{

    /**
    * UserRepository constructor.
    *
    * @param User $model
    */
   public function __construct(User $model)
   {
       parent::__construct($model);
   }

    public function user_exists_check($data){
        return $this->model->where('phone', $data['phone'])
                        ->orWhere('email', $data['email'])
                        // ->where('status',"!=",2)
                        ->first();
    }

    public function get_devices($user_id){
        return $this->model->with('devices')
                ->where('id',$user_id)->get()->first()->devices;
    }

    public function get_favorites($user_id,$type){
         // -- pending to do
        if(in_array($type,[1,2,3])){
            //join buisness table
         return  $this->model->join('user_favorites','user_favorites.user_id','=','users.id')->join('businesses','businesses.location_id','=','user_favorites.entity_id')
                ->where('users.id',$user_id)->where('user_favorites.type',$type)->whereNull('user_favorites.deleted_at')->orderBy('businesses.id', 'DESC')->get();
        }

        if(in_array($type,[4,5])){
            //get from movies table
            return $this->model->join('user_favorites','user_favorites.user_id','=','users.id')->join('movies','movies.property_id','=','user_favorites.entity_id')
                ->where('users.id',$user_id)->whereIn('user_favorites.type',array(4,5))->whereNull('user_favorites.deleted_at')->orderBy('movies.id', 'DESC')->get();

        }
        return $this->model->with('favorites')
                ->where('id',$user_id)->get()->first()->favorites;
    }

    public function get_feedbacks($user_id){
        return $this->model->with('feedbacks')
                ->where('id',$user_id)->get()->first()->feedbacks;
    }


}
