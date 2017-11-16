<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->increments('id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('fbID')->nullable();
            $table->string('customer_id')->nullable();
            $table->boolean('new_customer')->default(true);
            $table->string('card_type')->nullable();
            $table->string('last_four_card')->nullable();
            $table->integer('photo_id')->nullable();
            $table->integer('default_tip_rate')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->boolean('is_admin')->default(false);
            $table->string('role')->default('customer');
            $table->integer('employer_id')->nullable()->unsigned();
            $table->boolean('on_shift')->default(false);
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
