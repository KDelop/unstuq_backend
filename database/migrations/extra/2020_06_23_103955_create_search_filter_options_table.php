<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSearchFilterOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('search_filter_options', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('section_id');
            $table->string('label');
            $table->string('value');
            $table->string('count');
            $table->string('single_select');
            $table->string('parent_id');
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
        Schema::dropIfExists('search_filter_options');
    }
}
