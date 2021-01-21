<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DateIdea extends Model
{
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $fillable = ['title','description','image','likes','instructions','submitted_by','category','difficulty'];

    public function dateIdeaLike()
    {
        return $this->hasMany(DateIdeaLike::class, 'date_idea_id');
    }
}
