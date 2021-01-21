<?php

namespace App\Repositories\UserDevice;

use App\Models\UserDevice;
use App\Repositories\UserDevice\UserDeviceRepositoryInterface;
use App\Repositories\Contracts\RepositoryInterface;
use App\Repositories\Eloquent\Repository;

class UserDeviceRepository  extends Repository implements UserDeviceRepositoryInterface
{

    /**
    * UserDeviceRepository constructor.
    *
    * @param UserDevice $model
    */
   public function __construct(UserDevice $model)
   {
       parent::__construct($model);
   }

    public function check_device_exists($data){
        return $this->model->where('user_id',$data['user_id'])
                        ->where('device_uuid',$data['device_uuid'])
                        ->first();
    }
    public function update_player_id($user_id,$player_id){
         return $this->model->where('user_id',$user_id)->update([
                            'player_id' => $player_id]);
   }
}
