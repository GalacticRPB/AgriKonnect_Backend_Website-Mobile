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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->integer('order_id');
            $table->integer('seller_id');
            $table->integer('customer_id');
            $table->integer('product_id');
            $table->string('firstname');
            $table->string('middlename');
            $table->string('lastname');
            $table->string('order_name');
            $table->integer('order_qty');
            $table->integer('order_total');
            $table->string('review');
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
        Schema::dropIfExists('reviews');
    }
};
