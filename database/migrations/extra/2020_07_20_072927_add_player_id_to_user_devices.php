<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPlayerIdToUserDevices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_devices', function (Blueprint $table) {
            $table->text('player_id')->nullable()->after('device_name');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_devices', function (Blueprint $table) {
            $table->dropColumn('player_id');
        });
    }
}
