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
        Schema::create('carts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->index('carts_user_id_foreign');
            $table->unsignedBigInteger('shop_id')->nullable()->index('carts_shop_id_foreign');
            $table->unsignedBigInteger('order_id')->nullable();
            $table->unsignedBigInteger('subcategory_id')->nullable()->index('carts_subcategory_id_foreign');
            $table->integer('quantity');
            $table->boolean('active')->default(true);
            $table->string('p_name')->nullable();
            $table->longText('p_description')->nullable();
            $table->double('p_price')->nullable();
            $table->double('p_revenue')->nullable();
            $table->integer('p_offer')->nullable();
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
        Schema::dropIfExists('carts');
    }
};
