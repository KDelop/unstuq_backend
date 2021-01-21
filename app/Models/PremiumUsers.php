<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class PremiumUsers extends Model{
 	protected $table = 'premium_users';   
	protected $casts = [
		'id'=>'integer',
		'user_id'=>'integer',
	];
	protected $fillable = [
        'user_id','transaction_Date'
    ];
    public function User(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}