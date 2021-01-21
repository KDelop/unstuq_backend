<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMoviesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movies', function (Blueprint $table) {
            $table->string('movie_id');
            $table->string('title');
            $table->string('poster_url');
            $table->string('backdrop_url');
            $table->string('service_availability');
            $table->string('overview');
            $table->string('movie_cast');
            $table->string('popularity');
            $table->string('classification');
            $table->string('runtime');
            $table->string('genres');
            $table->string('tags');
            $table->date('released_on');
            $table->string('reelgood_url');
            $table->string('production_company');
            $table->string('imdb');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('movies');
    }
}
