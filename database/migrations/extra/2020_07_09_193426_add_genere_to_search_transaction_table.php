<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGenereToSearchTransactionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('search_transactions', function (Blueprint $table) {
            $table->integer('genre')->nullable();
            $table->string('network')->nullable();
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
            $table->dropColumn('genre');
            $table->dropColumn('network');
        });
    }
}
