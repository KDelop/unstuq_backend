<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserGroup extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'group_name', 'user_id', 'group_icon'
    ];

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->created_at = (string)gmdate('Y-m-d H:i:s');
        });
    }

    public function admin_user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function members(){
        return $this->belongsToMany(User::class, UserGroupMember::class)->withPivot('is_admin','created_at');
    }

    public function groupMembers(){
        return $this->hasMany(UserGroupMember::class,'user_id','id');
    }

    public function search_group_transaction() {
        return $this->belongsToMany(SearchTransactionGroup::class, 'user_group_id');
    }

    public function toArray()
    {
        $avatar = null;
        if($this->group_icon != null){
            $avatar = "uploads/".$this->group_icon;
        }

        return [
            'id' => $this->id,
            'name' => $this->group_name,
            'icon' =>  $avatar,
            'created_by' => $this->user_id,
            'created_at' => $this->created_at
        ];
    }
}
