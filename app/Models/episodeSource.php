<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class episodeSource extends Model{
	protected $primaryKey = 'episode_id';
	public $timestamps = false;
    protected $fillable = [
		'episode_id','source_id','web_link','ios_link','android_link','rental_cost_sd','rental_cost_hd','purchase_cost_sd','purchase_cost_hd'
	];
	public $timestamps = false;
	public function episode(){
		return $this->belongsToMany(episode::class, 'episode_id','episode_id');
 	}
 	public function source(){
		return $this->belongsToMany(source::class, 'source_id','id');
	}
}