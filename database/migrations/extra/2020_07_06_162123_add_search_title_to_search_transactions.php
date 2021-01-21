<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSearchTitleToSearchTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('search_transactions', function (Blueprint $table) {
            $table->string('search_title');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('search_transactions', function (Blueprint $table) {
            $table->string('search_title')->after('user_id');

        });
    }
}
