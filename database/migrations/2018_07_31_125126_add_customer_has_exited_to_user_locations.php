<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCustomerHasExitedToUserLocations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_locations', function (Blueprint $table) {
            $table->boolean('customer_exited')->after('exit_notification_sent')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_locations', function (Blueprint $table) {
            $table->dropColumn('customer_exited');
        });
    }
}
