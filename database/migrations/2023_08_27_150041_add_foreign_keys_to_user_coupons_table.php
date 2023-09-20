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
        Schema::table('user_coupons', function (Blueprint $table) {
            $table->foreign(['coupon_id'])->references(['id'])->on('coupons');
            $table->foreign(['user_id'])->references(['id'])->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_coupons', function (Blueprint $table) {
            $table->dropForeign('user_coupons_coupon_id_foreign');
            $table->dropForeign('user_coupons_user_id_foreign');
        });
    }
};
