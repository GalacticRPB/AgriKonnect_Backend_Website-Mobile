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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('seller_id');
            $table->integer('cart_id');
            $table->integer('product_id');
            $table->string('image')->nullable();
            $table->string('order_name');
            $table->integer('price');
            $table->integer('product_qty');
            $table->string('firstname');
            $table->string('middlename');
            $table->string('lastname');
            $table->string('shippingaddress');
            $table->integer('shippingfee');
            $table->integer('total_price');
            $table->string('mobilephone');
            $table->string('modeofpayment');
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
        Schema::dropIfExists('orders');
    }
};
