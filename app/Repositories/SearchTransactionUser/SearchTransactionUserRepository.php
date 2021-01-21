<?php

namespace App\Repositories\SearchTransactionUser;

use App\Models\SearchTransactionUser;
use App\Repositories\SearchTransactionUser\SearchTransactionUserRepositoryInterface;

use App\Repositories\Contracts\RepositoryInterface;
use App\Repositories\Eloquent\Repository;

class SearchTransactionUserRepository extends Repository implements SearchTransactionUserRepositoryInterface
{
    public function __construct(SearchTransactionUser $model)
    {
        parent::__construct($model);
    }
}