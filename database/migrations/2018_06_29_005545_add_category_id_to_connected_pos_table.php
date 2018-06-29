<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCategoryIdToConnectedPosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('connected_pos', function (Blueprint $table) {
            $table->string('clover_category_id')->nullable();;
        });

        Schema::table('user_locations', function (Blueprint $table) {
            $table->string('pos_customer_id')->nullable();;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('connected_pos', function (Blueprint $table) {
            $table->dropColumn('clover_category_id');
        });

        Schema::table('user_locations', function (Blueprint $table) {
            $table->dropColumn('pos_customer_id');
        });
    }
}
