<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserFavorite extends Model
{
    public $timestamps = false;
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'entity_id', 'user_id','type'
    ];
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
