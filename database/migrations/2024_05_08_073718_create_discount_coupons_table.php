<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discount_coupons', function (Blueprint $table) {
            $table->id();
            // discount cuopon code
            $table->string('code');
            // readable discount coupon code
            $table->string('name')->nullable();
            // description of coupon
            $table->string('description')->nullable();
            // max uses of coupon
            $table->integer('max_uses')->nullable(); 
            // how many time a user can use this coupon
            $table->integer('max_uses_user')->nullable();
            // coupon type percentage or fixed
            $table->enum('type',['percentage','fixed'])->default('fixed'); 
            // the amount to discount based on type
            $table->double('discount_amount',10,2);
            // minimum amount to use this coupon
            $table->double('min_amount',10,2)->nullable();
            // coupon status
            $table->integer('status')->default(1);
            // coupon start date
            $table->date('start_date')->nullable();
            // coupon end date
            $table->date('expire_date')->nullable();
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
        Schema::dropIfExists('discount_coupons');
    }
};
