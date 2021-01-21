<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEpisodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('episodes', function (Blueprint $table) {
            $table->string('episode_id');
            $table->string('show_id');
            $table->string('title');
            $table->string('overview');
            $table->string('episode_image_url');
            $table->string('service_availability');
            $table->string('released_on');
            $table->string('runtime');
            $table->string('sequence_number');
            $table->string('episode_number');
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
        Schema::dropIfExists('episodes');
    }
}
