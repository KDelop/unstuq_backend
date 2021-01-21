<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserFavorites extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_favorites', function (Blueprint $table) {
            
            $table->unsignedBigInteger('entity_id');
            // $table->foreign('entity_id')->references('id')->on('businesses');

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');

            $table->tinyInteger("type")->comments("1:restaurants,2:attractions,3:hotel,4:movie,5:tv show");
            
            $table->datetime('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_favorites');
    }
}
