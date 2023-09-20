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
        Schema::table('orders', function (Blueprint $table) {
            $table->foreign(['coupon_id'])->references(['id'])->on('coupons');
            $table->foreign(['user_id'])->references(['id'])->on('users');
            $table->foreign(['order_payment_id'])->references(['id'])->on('order_payments');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign('orders_coupon_id_foreign');
            $table->dropForeign('orders_user_id_foreign');
            $table->dropForeign('orders_order_payment_id_foreign');
        });
    }
};
