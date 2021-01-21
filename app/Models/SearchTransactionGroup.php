<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SearchTransactionGroup extends Model
{
    //
    public function user_group(){
            return $this->hasMany(UserGroup::class, 'id', 'user_group_id');
    }
}
