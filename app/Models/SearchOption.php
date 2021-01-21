<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SearchOption extends Model{
	protected $table = 'search_options';   
	protected $casts = [
		'id'=>'integer',
		'active'=>'integer',
	];
	protected $fillable = [
        'searchType','searchLabel','searchText','active'
    ];
}