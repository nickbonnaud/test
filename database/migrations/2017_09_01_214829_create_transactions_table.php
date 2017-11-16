<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('profile_id')->unsigned();
            $table->foreign('profile_id')->references('id')->on('profiles')->onDelete('cascade');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('employee_id')->unsigned()->nullable();
            $table->boolean('paid')->default(false);
            $table->boolean('customer_first_transaction')->default(false);
            $table->boolean('bill_closed')->default(false);
            $table->integer('status')->nullable();
            $table->integer('outside_geofence_count')->default(0)->unsigned();
            $table->string('splash_id')->nullable();
            $table->string('notification_id')->nullable();
            $table->boolean('qb_synced')->default(false);
            $table->integer('deal_id')->nullable();
            $table->boolean('redeemed')->nullable();
            $table->json('products')->nullable();
            $table->integer('tax')->nullable();
            $table->integer('tips')->nullable();
            $table->integer('net_sales')->nullable();
            $table->integer('total')->nullable();
            $table->boolean('is_refund')->default(false);
            $table->boolean('refunded')->default(false);
            $table->boolean('refund_full')->default(false);
            $table->json('refund_products')->nullable();
            $table->integer('refund_tax')->unsigned()->nullable();
            $table->integer('refund_amount')->unsigned()->nullable();
            $table->string('refund_id')->nullable();
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
        Schema::dropIfExists('transactions');
    }
}
