<?php

namespace App\Repositories\SearchTransaction;

interface SearchTransactionRepositoryInterface
{
    public function save_search_results($searching_user, $result_array, $data);
}