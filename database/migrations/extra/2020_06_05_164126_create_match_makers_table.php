<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMatchMakersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('match_makers', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');

            $table->unsignedBigInteger('search_transaction_id');
            $table->foreign('search_transaction_id')->references('id')->on('search_transactions');

            $table->integer('entity_id');
            $table->tinyInteger('like_dislike')->comments('1:like,2:dislike');

            $table->datetime('created_at');

            $table->index(['search_transaction_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('match_makers');
    }
}
