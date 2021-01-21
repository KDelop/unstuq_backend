<?php

namespace App\Repositories\Business;

use App\Models\Business;
use App\Repositories\Business\BusinessRepositoryInterface;

use App\Repositories\Contracts\RepositoryInterface;
use App\Repositories\Eloquent\Repository;

class BusinessRepository extends Repository implements BusinessRepositoryInterface
{
    public function __construct(Business $model)
    {
        parent::__construct($model);
    }
}