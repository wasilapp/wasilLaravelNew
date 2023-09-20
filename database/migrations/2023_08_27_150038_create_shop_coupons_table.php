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
        Schema::create('shop_coupons', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('shop_id');
            $table->foreign(['shop_id'])->references(['id'])->on('shops');

            $table->unsignedBigInteger('coupon_id');
            $table->foreign(['coupon_id'])->references(['id'])->on('coupons');

/*             $table->unsignedBigInteger('coupon_id')->index('shop_coupons_coupon_id_foreign');
 */            $table->timestamps();

            $table->unique(['shop_id', 'coupon_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_coupons');
    }
};
