<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class show extends Model{
	protected $casts = [
        'show_id' => 'string',
        'show_cast'=>'array',
        'tags'=>'array',
    ];
	protected $primaryKey = 'show_id';
	public $timestamps = false;
	protected $fillable = [
		'show_id','title','poster_url','backdrop_url','overview','show_cast','popularity','classification','runtime','genres','tags','released_on','status','reelgood_url','production_company','network','imdb'
	];
	public function toArray()
    {
        
        return [
            'show_id' => $this->movie_id,
            'title' => $this->title,
            'poster_url' => $this->poster_url,
            'backdrop_url' => $this->backdrop_url,
            // 'service_availability' => $this->service_availability,
            'overview' => $this->overview,
            // 'show_cast' => $this->show_cast,
            'show_cast' => json_decode($this->show_cast),
            'popularity' => $this->popularity,
            'classification' => $this->classification,
            'runtime' => $this->runtime,
            'genres' => $this->genres,
            // 'tags' => json_decode($this->tags),
            'released_on' => $this->released_on,
            'status' => $this->status,
            'reelgood_url' => $this->reelgood_url,
            'production_company' => json_decode($this->production_company),
            'network' => $this->network,
            'imdb' => $this->imdb,
        ];
    }
	public function episode(){
		return $this->hasMany(episode::class, 'show_id','show_id');
	}
}
