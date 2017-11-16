<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostAnalyticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('post_analytics', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('profile_id')->unsigned();
            $table->foreign('profile_id')->references('id')->on('profiles');
            $table->integer('post_id')->unsigned();
            $table->foreign('post_id')->references('id')->on('posts');
            $table->boolean('viewed')->default(false);
            $table->dateTime('viewed_on')->nullable();
            $table->boolean('shared')->default(false);
            $table->dateTime('shared_on')->nullable();
            $table->boolean('bookmarked')->default(false);
            $table->dateTime('bookmarked_on')->nullable();
            $table->boolean('transaction_resulted')->default(false);
            $table->dateTime('transaction_on')->nullable();
            $table->integer('total_revenue')->default(0);
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
        Schema::dropIfExists('post_analytics');
    }
}
