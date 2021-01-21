<?php

namespace App\Repositories\SearchFilterOption;

use App\Models\SearchFilterOptions;
use App\Repositories\SearchFilterOption\SearchFilterOptionRepositoryInterface;

use App\Repositories\Contracts\RepositoryInterface;
use App\Repositories\Eloquent\Repository;

class SearchFilterOptionRepository extends Repository implements SearchFilterOptionRepositoryInterface
{
    public function __construct(SearchFilterOptions $model)
    {
        parent::__construct($model);
    }
}