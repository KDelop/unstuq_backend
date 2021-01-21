<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypesToMoviesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('movies', function (Blueprint $table) {
            $table->tinyInteger('type')->comments("4:movie,5:tv");
            $table->integer('vote_count')->nullable();
            $table->float('vote_average')->nullable();
            $table->text('overview')->nullable();
            $table->string('genre')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('movies', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->dropColumn('vote_count');
            $table->dropColumn('vote_average');
            $table->dropColumn('overview');
            $table->dropColumn('genre');
        });
    }
}
