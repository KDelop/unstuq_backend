<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DateIdeaLike extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $fillable = ['date_idea_id', 'user_id', 'status'];

    protected $primaryKey = 'id';

    public function dateIdea()
    {
        return $this->belongsTo(DateIdea::class, 'date_idea_id','id');
    }
}
