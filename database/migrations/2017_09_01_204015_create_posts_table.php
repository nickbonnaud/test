<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('profile_id')->unsigned();
            $table->foreign('profile_id')->references('id')->on('profiles')->onDelete('cascade');
            $table->string('title')->nullable();
            $table->string('body')->nullable();
            $table->text('message')->nullable();
            $table->string('fb_post_id')->nullable();
            $table->string('insta_post_id')->nullable();
            $table->integer('views')->default(0);
            $table->integer('shares')->default(0);
            $table->integer('bookmarks')->default(0);
            $table->integer('total_interactions')->default(0);
            $table->integer('total_revenue')->default(0);
            $table->integer('photo_id')->unsigned()->nullable();
            $table->foreign('photo_id')->references('id')->on('photos');
            $table->string('social_photo_url')->nullable();
            $table->date('event_date')->nullable();
            $table->boolean('is_redeemable')->default(false);
            $table->string('deal_item')->nullable();
            $table->integer('price')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamps();
            $table->timestamp('published_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts');
    }
}
