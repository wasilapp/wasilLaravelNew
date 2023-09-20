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
        Schema::table('carts', function (Blueprint $table) {
            $table->foreign(['shop_id'])->references(['id'])->on('shops');
            $table->foreign(['user_id'])->references(['id'])->on('users');
            $table->foreign(['subcategory_id'])->references(['id'])->on('sub_categories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->dropForeign('carts_shop_id_foreign');
            $table->dropForeign('carts_user_id_foreign');
            $table->dropForeign('carts_subcategory_id_foreign');
        });
    }
};
