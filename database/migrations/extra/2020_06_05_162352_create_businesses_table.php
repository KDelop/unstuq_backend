<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusinessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('businesses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');

            $table->string('location_id',50);
            // $table->string('address');

            $table->string('longitude');
            $table->string('latitude');

            $table->decimal('rating',2,2);
            $table->string('ranking');

            $table->text('info');

            // $table->string('price_range');
            $table->tinyInteger('type')->comments("1:restaurants,2:attractions,3:hotel");

            // $table->string('image');
            $table->timestamps();

            $table->index(['location_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('businesses');
    }
}
