<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeConnectedPosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('connected_pos', function (Blueprint $table) {
            $table->string('account_type')->nullable()->change();
            $table->string('token')->nullable()->change();
            $table->string('merchant_id')->nullable()->change();
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
            $table->string('account_type')->change();
            $table->string('token')->change();
            $table->string('merchant_id')->change();
        });
    }
}
