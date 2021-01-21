<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Experience extends Model{
	protected $table = 'experiences';
    protected $casts = [
        'choices'=>'array',
    ];
	protected $primaryKey = 'experience_id';
	public $timestamps = false;
	protected $fillable = [
		'experience_id','user_id','choices','experience_date','title','image','experience_date_time'
	];
	public function toArray()
    {
        return [
            'experience_id' => $this->experience_id,
            'title' => $this->title,
            'image' => $this->image,
            'user_id' => $this->user_id,
            'name' => $this->user->name,
            'choices' => $this->choices,
            'experience_date_time' => $this->experience_date_time,
            'shared_with'=>$this->sharedExperience,
            // 'shared_with' => [
            //     // 'id'=>$this->userExperience->user_id,
            //     'id'=>$this->userExperience->user->name,
            // ],
        ];
    }
    public function user(){
    	return $this->belongsTo(User::class, 'user_id','id');
    }
    public function userExperience(){
    	return $this->hasMany(UserExperience::class, 'experience_id','experience_id');
    }
    public function sharedExperience(){
        return $this->hasMany(UserSharedExperience::class, 'experience_id','experience_id');
    }
}