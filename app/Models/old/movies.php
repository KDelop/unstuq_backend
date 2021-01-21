<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class movies extends Model
{
    protected $fillable = [
        'name', 'property_id', 'details','type','vote_count',  'vote_average', 'overview', 'genre','network'
    ];
    public $timestamps = false;
}
