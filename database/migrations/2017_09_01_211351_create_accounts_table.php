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
            $table->string('accountUserFirst')->nullable();
            $table->string('accountUserLast')->nullable();
            $table->integer('ownership')->unsigned()->nullable();
            $table->string('ownerEmail')->nullable();
            $table->string('accountEmail');
            $table->date('dateOfBirth')->nullable();
            $table->string('ssn')->nullable();
            $table->string('indivStreetAddress')->nullable();
            $table->string('indivCity')->nullable();
            $table->string('indivState')->nullable();
            $table->string('indivZip')->nullable();
            $table->string('legalBizName');
            $table->integer('businessType')->unsigned();
            $table->date('established');
            $table->integer('annualCCSales')->unsigned();
            $table->string('bizTaxId');
            $table->string('bizStreetAddress');
            $table->string('bizCity');
            $table->string('bizState');
            $table->string('bizZip');
            $table->string('phone');
            $table->string('accountNumber')->nullable();
            $table->string('routing')->nullable();
            $table->integer('method')->unsigned()->nullable();
            $table->string('splashId')->nullable();
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
