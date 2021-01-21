<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    public $timestamps = false;
    public $fillable = ['name','location_id','longitude','latitude','rating','ranking','info','type'];
}
