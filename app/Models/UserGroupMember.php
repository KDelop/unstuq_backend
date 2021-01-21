<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserGroupMember extends Model
{
    public $timestamps = false;

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->created_at = (string)gmdate('Y-m-d H:i:s');
        });
    }
    
    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function group() {
        return $this->belongsTo(UserGroup::class, 'user_group_id', 'id');
    }
}
