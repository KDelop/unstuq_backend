<?php

use Illuminate\Database\Seeder;
use App\Models\Genre;

class GenreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Genre::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $url = "https://api.themoviedb.org/3/genre/movie/list";
        $data['api_key'] =  env('API_KEY');
        $data['language'] = "en-US";
        $results = callAPI('GET', $url, $data);

        foreach( $results->genres as $genre){
            Genre::create([
                'name' => $genre->name,
                'genre_id' =>  $genre->id,
                'type' => "movie",
                'active' => 1
            ]);
        }

        $url = "https://api.themoviedb.org/3/genre/tv/list";
        $data['api_key'] =  env('API_KEY');
        $data['language'] = "en-US";
        $results = callAPI('GET', $url, $data);
        
        foreach( $results->genres as $genre){
            Genre::create([
                'name' => $genre->name,
                'genre_id' =>  $genre->id,
                'type' => "tv",
                'active' => 1
            ]);
        }
        
    }
}
