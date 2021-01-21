<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSearchTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('search_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');

            $table->string('search_user_type')->comments("1:solo,2:group");
            $table->string('search_type')->comments("1:restaurants,2:attractions,3:hotels,4:movie,5:tv");
            $table->tinyInteger('pending_notification')->default("0");

            $table->string('meet_time')->nullable();
            $table->datetime('deadline')->nullable();

            $table->string('location_name')->default("null");
            $table->string('location_longitude');
            $table->string('location_latitude');

            $table->text('results');
            $table->integer('matched_entity_id')->default('0');
            $table->longText('matched_entity_reviews')->nullable();
            
            $table->tinyInteger('live')->default('1');
            $table->tinyInteger('push_notification_status')->default('0')->comments('0:pending,1:complete');

            $table->tinyInteger('status')->default('0')->comments('1:complete,0:pending,2:no match');

            $table->datetime('created_at');

            $table->index(['status','live']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('search_transactions');
    }
}
