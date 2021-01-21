<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class source extends Model{
	protected $table = 'sources';
    protected $primaryKey = 'id';
    protected $timestamp = false;
    protected $casts = [
        'id' => 'string'
    ];
    protected $fillable = [
    	'id','source_name'
    ];
    public function toArray(){
        $in_profile = false;
        if(count($this->UserSource)>0){
            $in_profile = true;
        }
        return [
            'id'=>$this->id,
            'in_profile'=>$in_profile,
            'source_name'=>$this->source_name,
            'icon'=>$this->icon,
        ];
    }
    public $timestamps = false;
    public function movieSource(){
    	return $this->hasMany(movieSource::class, 'source_id','id');
    }
    public function episodeSource(){
        return $this->hasMany(episodeSource::class, 'source_id','id');
    }
    public function UserSource(){
        return $this->hasMany(UserSource::class);
    }
    public function User()
    {
        return $this->belongsToMany(User::class, 'user_id', 'id');
    }
}
