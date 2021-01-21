<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class UserSource extends Model{
	protected $table = 'user_sources';   
	protected $casts = [
		'user_id'=>'integer',
	];
	protected $fillable = [
        'user_id', 'source_id'
    ];
	// public function toArray(){
	// 	return [
	// 		'source_id '=>$this->source_id,
	// 		'in_profile'=>true,
	// 	];
	// }
	public function source(){
        return $this->belongsTo(source::class, 'source_id','id');
    }
    public function User(){
        return $this->belongsTo(User::class);
    }
}