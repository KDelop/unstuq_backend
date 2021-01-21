<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MatchMaker extends Model
{
    public $timestamps = false;
    public $fillable = ['user_id','search_transaction_id','entity_id','like_dislike','created_at'];
}
