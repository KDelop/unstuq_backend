<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SearchTransaction extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'search_type','meet_time','location_longitude','location_latitude',
        'results','created_at','search_user_type','matched_entity_reviews', 'deadline','location_name','search_title','genre','network','search_parameters'];

    public $timestamps = false;

    public function toArray(){
        return [
            'position'=>$this->position,
            'title'=>$this->title,
            'place_id'=>$this->place_id,
            'lsig'=>$this->lsig,
            'place_id_search'=>$this->place_id_search,
            'rating'=>$this->rating,
            'reviews'=>$this->reviews,
            'address'=>$this->address,
            'thumbnail'=>$this->thumbnail,
            'gps_coordinates'=>$this->gps_coordinates,
            'votes'=>null,
        ];
    }

    protected $dates = ['deleted_at'];

    public function groups(){
        return $this->belongsToMany(UserGroup::class, SearchTransactionGroup::class);
    }

    public function users()
    {
       return $this->belongsToMany(User::class, SearchTransactionUser::class)->withPivot('status');
    }

    public function checkGroups(){
        return $this->hasOne(SearchTransactionGroup::class,'search_transaction_id','id');
    }
}
