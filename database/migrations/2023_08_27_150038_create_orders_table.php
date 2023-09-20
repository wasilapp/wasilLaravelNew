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
            $table->bigIncrements('id');
            $table->integer('status')->default(1);
            $table->integer('order_type');
            $table->double('order');
            $table->double('shop_revenue');
            $table->double('admin_revenue');
            $table->double('delivery_fee');
            $table->double('total');
            $table->integer('otp');
            $table->double('coupon_discount')->nullable();
            $table->double('latitude')->nullable();
            $table->double('longitude')->nullable();
            $table->unsignedBigInteger('coupon_id')->nullable()->index('orders_coupon_id_foreign');
            $table->unsignedBigInteger('delivery_boy_id')->nullable();
            $table->unsignedBigInteger('user_id')->index('orders_user_id_foreign');
            $table->unsignedBigInteger('address_id')->nullable();
            $table->unsignedBigInteger('shop_id')->nullable();
            $table->unsignedBigInteger('order_payment_id')->index('orders_order_payment_id_foreign');
            $table->integer('count');
            $table->boolean('type');
            $table->boolean('is_notification')->default(true);
            $table->boolean('is_paid')->default(false);
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
