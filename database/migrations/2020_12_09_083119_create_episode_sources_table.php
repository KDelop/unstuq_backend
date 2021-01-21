<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEpisodeSourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('episode_sources', function (Blueprint $table) {
            $table->string('episode_id');
            $table->string('source_id');
            $table->string('web_link');
            $table->string('ios_link');
            $table->string('android_link');
            $table->string('rental_cost_sd');
            $table->string('rental_cost_hd');
            $table->string('purchase_cost_sd');
            $table->string('purchase_cost_hd');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('episode_sources');
    }
}
