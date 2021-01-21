<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class MovieCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            // 'data' => $this->collection,
            // 'movie_id' => $this->movie_id,
            'title' => $this->title,
            // 'poster_url' => $this->poster_url,
            // 'backdrop_url' => $this->backdrop_url,
            // 'service_availability' => $this->service_availability,
            // 'overview' => $this->overview,
            // 'movie_cast' => $this->movie_cast,
            // 'popularity' => $this->popularity,
            // 'classification' => $this->classification,
            // 'runtime' => $this->runtime,
            // 'genres' => $this->genres,
            // 'tags' => $this->tags,
            // 'released_on' => $this->released_on,
            // 'reelgood_url' => $this->reelgood_url,
            // 'production_company' => $this->production_company,
            // 'imdb' => $this->imdb,
            // 'movieSource' => 'movieSource',
            // 'movieSource' => $this->movieSource,
            // 'users' => UserResource::collection($this->users),
        ];
    }
}
