<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class UserFavourite extends Model{
	protected $primaryKey = 'id';
	protected $table = 'user_favorites';
    protected $casts = [
        'entity_id' => 'integer',
        'user_id' => 'integer',
        // 'data' => 'array',
    ];
    protected $fillable = [
    	'entity_id', 'user_id','data','deleted_at','title'
    ];
    public function User(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

     public function toArray(){
     	return [
     		'id' => $this->id,
     		'entity_id' => $this->entity_id,
     		'user_id' => $this->user_id,
     		'data' => json_decode($this->data),
     		'title' => $this->title,
     	];
     }
}
