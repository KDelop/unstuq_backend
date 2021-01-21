<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shows', function (Blueprint $table) {
            $table->string('show_id');
            $table->string('title');
            $table->string('poster_url');
            $table->string('backdrop_url');
            $table->string('overview');
            $table->string('show_cast');
            $table->string('popularity');
            $table->string('classification');
            $table->string('runtime');
            $table->string('genres');
            $table->string('tags');
            $table->string('released_on');
            $table->string('status');
            $table->string('reelgood_url');
            $table->string('production_company');
            $table->string('network');
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
        Schema::dropIfExists('shows');
    }
}
