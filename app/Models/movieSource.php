<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class movieSource extends Model{
	protected $primaryKey = 'movie_id';
    protected $fillable = [
		'movie_id','source_id','web_link','ios_link','android_link','rental_cost_sd','rental_cost_hd','purchase_cost_sd','purchase_cost_hd'
	];
	protected $casts = [
        'movie_id' => 'string'
    ];
	public function source(){
		return $this->belongsToMany(source::class, 'source_id','id');
	}
	public function movie(){
		return $this->belongsToMany(movie::class, 'movie_id','movie_id');
	}
}
