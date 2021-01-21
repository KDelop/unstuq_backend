<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class UserCredit extends Model{
 	protected $table = 'user_credits';   
	protected $casts = [
		'id'=>'integer',
		'user_id'=>'integer',
		'balance'=>'integer',
	];
	protected $fillable = [
        'user_id', 'balance'
    ];
    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}