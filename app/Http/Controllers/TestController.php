<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserGroup;
use App\Models\User;

class TestController extends Controller
{
    public function test(){
        $url = "https://api.themoviedb.org/3/genre/movie/list";
        $data['api_key'] =  env('API_KEY');
        $data['language'] = "en-US";
        $results = callAPI('GET', $url, $data);
        dd($results->genres);
    }
}
