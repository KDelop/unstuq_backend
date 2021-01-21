<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone');
            $table->string('avatar',255)->nullable();
            $table->string('access_code',10)->nullable();
            $table->string('fcm_token')->nullable();

            $table->tinyInteger('user_type')->comment("1:normal,2:business user")->default("1");

            // $table->string('verified_by')->comment("1:email,2:phone");
            $table->timestamp('last_verified_at')->nullable();
            $table->tinyInteger('status')->default('0');

            // normal user login 
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();

            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
