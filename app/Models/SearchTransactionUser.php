<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SearchTransactionUser extends Model
{
    protected $fillable = [
        'user_id', 'search_transaction_id', 'status'
    ];
    public $timestamps = false;
}
