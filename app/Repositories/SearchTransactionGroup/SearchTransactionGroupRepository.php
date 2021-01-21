<?php

namespace App\Repositories\SearchTransactionGroup;

use App\Models\SearchTransactionGroup;
use App\Repositories\SearchTransactionGroup\SearchTransactionGroupRepositoryInterface;

use App\Repositories\Contracts\RepositoryInterface;
use App\Repositories\Eloquent\Repository;

class SearchTransactionGroupRepository extends Repository implements SearchTransactionGroupRepositoryInterface
{
    public function __construct(SearchTransactionGroup $model)
    {
        parent::__construct($model);
    }

    public function getgroupdetails($search_transaction_id){
        return SearchTransactionGroup::with('user_group')->where('search_transaction_id',$search_transaction_id)->get();
    }
}
