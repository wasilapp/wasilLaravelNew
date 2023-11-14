<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCouponDeliveryBoyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupon_delivery_boy', function (Blueprint $table) {
            $table->foreignId('delivery_boy_id');
            $table->foreignId('coupon_id');

            $table->primary(['delivery_boy_id', 'coupon_id']);
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
        Schema::dropIfExists('coupon_delivery_boy');
    }
}
