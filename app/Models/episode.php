<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class episode extends Model{
	protected $primaryKey = 'episode_id';
	public $timestamps = false;
	protected $fillable = [
		'episode_id','show_id','title,''overview','episode_image_url','service_availability','released_on','runtime','sequence_number','episode_number','imdb'
	];
	public $timestamps = false;
 	public function episodeSource(){
		return $this->hasMany(episodeSource::class, 'episode_id','episode_id');
 	}
 	public function show(){
		return $this->belongsTo(show::class, 'show_id','show_id');
	}
}
