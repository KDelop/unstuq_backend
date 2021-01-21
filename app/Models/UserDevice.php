<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDevice extends Model
{
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'device_uuid', 'device_type','device_name','player_id'
    ];

    public $timestamps = true;

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

}
