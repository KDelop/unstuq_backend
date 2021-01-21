<?php

namespace App\Repositories\UserGroupMember;

use App\Models\UserGroupMember;
use App\Repositories\UserGroupMember\UserGroupMemberRepositoryInterface;

use App\Repositories\Contracts\RepositoryInterface;
use App\Repositories\Eloquent\Repository;

class UserGroupMemberRepository extends Repository implements UserGroupMemberRepositoryInterface
{

    public function __construct(UserGroupMember $model)
    {
        parent::__construct($model);
    }
    public function get_groups($users_arr){
    	return $this->model->whereIn('user_id',$users_arr)->get();
    }

}