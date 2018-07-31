<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCustomerExitedOnTimeToUserLocations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_locations', function (Blueprint $table) {
            $table->timestamp('exited_on')->nullable()->after('customer_exited');
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
            $table->dropColumn('exited_on');
        });
    }
}
