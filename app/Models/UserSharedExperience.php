<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class UserSharedExperience extends Model{
	protected $table = 'user_experiences';
	protected $primaryKey = 'id';
	protected $fillable = [
		'experience_id','user_id'
	];
	public function toArray(){
        return [
            // 'experience_id' => [
            	'id' => $this->user->id,
            	'name' => $this->user->name,
            	'avatar' => $this->user->avatar,
            	'phone' => $this->user->phone,
            // ],
        ];
    }
    public function user(){
    	return $this->belongsTo(User::class, 'user_id','id');
    }
    // public function experience(){
    // 	return $this->belongsTo(Experience::class, 'experience_id','experience_id');
    // }
}