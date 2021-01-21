<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class movie extends Model{
	protected $primaryKey = 'movie_id';
    protected $casts = [
        'movie_id' => 'string',
        // 'genres'=>'array'
    ];
    protected $fillable = [
    	'title', 'poster_url','backdrop_url','service_availability',  'overview', 'movie_cast', 'popularity','classification','runtime','genres','tags','released_on','reelgood_url','production_company','network','imdb'
    ];
    public function movieSource(){
		return $this->hasMany(movieSource::class, 'movie_id','movie_id');
	}
	public function toArray()
    {
        
        return [
            'movie_id' => $this->movie_id,
            'title' => $this->title,
            'poster_url' => $this->poster_url,
            'backdrop_url' => $this->backdrop_url,
            'service_availability' => $this->service_availability,
            'overview' => $this->overview,
            'movie_cast' => $this->movie_cast,
            'popularity' => $this->popularity,
            'classification' => $this->classification,
            'runtime' => $this->runtime,
            'genres' => json_decode($this->genres,true),
            // 'genres' => $this->genres,
            'tags' => $this->tags,
            'released_on' => $this->released_on,
            'reelgood_url' => $this->reelgood_url,
            'production_company' => json_decode($this->production_company),
            'imdb' => $this->imdb,
            // 'movieSource' => $this->movieSource,
            // 'name' => $this->name,
            // 'avatar' => $avatar,
            // 'email' => $this->email,
            // 'phone' => $this->phone,
            // 'entity_id' => $this->entity_id,
            // 'location_string' => $location_string,
            // 'favourite_icon' => $favourite_icon,
            // 'rating' => (string)$rating,
            // 'type' => $this->type,
            // 'status' => $status,
            // 'created_at' => $this->created_at
        ];
    }
}
