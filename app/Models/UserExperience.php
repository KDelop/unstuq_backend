<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class UserExperience extends Model{
	protected $table = 'user_experiences';
	protected $primaryKey = 'id';
	public $timestamps = false;
	protected $fillable = [
		'experience_id','user_id'
	];
	public function toArray(){
        return [
            'id' => $this->id,
            'experience_id' => $this->experience_id,
            'user_id' => $this->user_id,
            'name'=>$this->user->name,
            'experience'=>[
                'experience_id'=>$this->experience->experience_id,
                'title'=>$this->experience->title,
                'image'=>$this->experience->image,
                'shared_by'=>$this->user->name,
                'experience_date_time'=>$this->experience->experience_date_time,
                'choices'=>json_decode($this->experience->choices),
            ],
        ];
    }
    public function user(){
    	return $this->belongsTo(User::class, 'user_id','id');
    }
    public function experience(){
    	return $this->belongsTo(Experience::class, 'experience_id','experience_id');
    }
}