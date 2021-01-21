<?php

namespace App\Repositories\UserGroup;

use App\Models\UserGroup;
use App\Repositories\UserGroup\UserGroupRepositoryInterface;

use App\Repositories\Contracts\RepositoryInterface;
use App\Repositories\Eloquent\Repository;

class UserGroupRepository extends Repository implements UserGroupRepositoryInterface
{
    public function __construct(UserGroup $model)
    {
        parent::__construct($model);
    }
    public function get_group_details($user_id){
        return UserGroup::where('user_id',$user_id);

    }
}
