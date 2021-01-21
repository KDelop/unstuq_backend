<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserFeedbacksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_feedbacks', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->tinyInteger('category')->comment('1:bug,2:Enhancement');
            $table->text('message');

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_feedbacks');
    }
}
