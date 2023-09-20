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
        Schema::table('shop_revenues', function (Blueprint $table) {
            $table->foreign(['order_id'])->references(['id'])->on('orders');
            $table->foreign(['shop_id'])->references(['id'])->on('shops');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shop_revenues', function (Blueprint $table) {
            $table->dropForeign('shop_revenues_order_id_foreign');
            $table->dropForeign('shop_revenues_shop_id_foreign');
        });
    }
};
