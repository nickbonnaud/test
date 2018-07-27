<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTenderIdToConnectedPosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('connected_pos', function (Blueprint $table) {
            $table->string('clover_tender_id')->after("clover_category_id")->nullable();
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
            $table->dropColumn('clover_tender_id');
        });
    }
}
