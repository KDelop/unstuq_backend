<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSearchTransactionGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('search_transaction_groups', function (Blueprint $table) {
            $table->unsignedBigInteger('user_group_id');
            $table->foreign('user_group_id')->references('id')->on('user_groups');

            $table->unsignedBigInteger('search_transaction_id');
            $table->foreign('search_transaction_id')->references('id')->on('search_transactions');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('search_transaction_groups');
    }
}
