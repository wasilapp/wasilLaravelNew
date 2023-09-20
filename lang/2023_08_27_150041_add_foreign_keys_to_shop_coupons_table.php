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
        Schema::table('shop_coupons', function (Blueprint $table) {
            
           // $table->foreign(['shop_id'])->references(['id'])->on('shops');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shop_coupons', function (Blueprint $table) {
            $table->dropForeign('shop_coupons_shop_id_foreign');
        });
    }
};
