<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::create('profiles', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('business_name');
            $table->integer('city_id')->unsigned()->nullable();
            $table->string('slug');
            $table->string('website');
            $table->text('description');
            $table->string('fb_page_id')->nullable();
            $table->string('fb_app_id')->nullable();
            $table->string('insta_account_id')->nullable();
            $table->string('insta_account_token')->nullable();
            $table->string('google_id')->nullable();
            $table->decimal('google_rating', 2, 1)->nullable();
            $table->string('square_token')->nullable();
            $table->string('connected')->nullable();
            $table->boolean('connected_qb')->default(false);
            $table->boolean('tip_tracking_enabled')->default(false);
            $table->string('review_url')->nullable();
            $table->string('review_intro')->nullable();
            $table->boolean('approved')->default(false);
            $table->boolean('featured')->default(false);
            $table->integer('logo_photo_id')->unsigned()->nullable();
            $table->foreign('logo_photo_id')->references('id')->on('photos');
            $table->integer('hero_photo_id')->unsigned()->nullable();
            $table->foreign('hero_photo_id')->references('id')->on('photos');
            $table->integer('tax_id')->unsigned()->nullable();
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
        Schema::dropIfExists('profiles');
    }
}
