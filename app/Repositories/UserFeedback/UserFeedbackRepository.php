<?php

namespace App\Repositories\UserFeedback;

use App\Models\UserFeedbacks;
use App\Repositories\UserFeedback\UserFeedbackRepositoryInterface;

use App\Repositories\Contracts\RepositoryInterface;
use App\Repositories\Eloquent\Repository;

class UserFeedbackRepository extends Repository implements UserFeedbackRepositoryInterface
{

    public function __construct(UserFeedbacks $model)
    {
        parent::__construct($model);
    }

}