<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('profile_id')->unsigned();
            $table->foreign('profile_id')->references('id')->on('profiles')->onDelete('cascade');
            $table->string('slug');
            $table->string('account_user_first')->nullable();
            $table->string('account_user_last')->nullable();
            $table->integer('ownership')->unsigned()->nullable();
            $table->string('owner_email')->nullable();
            $table->string('account_email')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('ssn')->nullable();
            $table->string('indiv_street_address')->nullable();
            $table->string('indiv_city')->nullable();
            $table->string('indiv_state')->nullable();
            $table->string('indiv_zip')->nullable();
            $table->string('legal_biz_name');
            $table->integer('business_type')->unsigned()->nullable();
            $table->date('established')->nullable();
            $table->integer('annual_cc_sales')->unsigned()->nullable();
            $table->string('biz_tax_id')->nullable();
            $table->string('biz_street_address');
            $table->string('biz_city');
            $table->string('biz_state');
            $table->string('biz_zip');
            $table->string('phone');
            $table->string('account_number')->nullable();
            $table->string('routing')->nullable();
            $table->integer('method')->unsigned()->nullable();
            $table->string('splash_id')->nullable();
            $table->string('status');
            $table->integer('pockeyt_qb_id')->nullable();
            $table->timestamp('qb_connected_date')->nullable();
            $table->integer('pockeyt_qb_account')->nullable();
            $table->integer('pockeyt_item')->nullable();
            $table->integer('pockeyt_payment_method')->nullable();
            $table->integer('pockeyt_qb_tips_account')->nullable();
            $table->integer('pockeyt_tips_item')->nullable();
            $table->integer('pockeyt_qb_taxcode')->nullable();
            $table->string('square_location_id')->nullable();
            $table->string('square_category_id')->nullable();
            $table->string('square_item_id')->nullable();
            $table->boolean('pockeyt_lite_enabled')->default(false);
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
        Schema::dropIfExists('accounts');
    }
}
