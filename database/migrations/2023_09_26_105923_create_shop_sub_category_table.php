<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopSubCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shop_sub_category', function (Blueprint $table) {
            $table->foreignId('sub_category_id');
            $table->foreignId('shop_id');
            $table->string('price')->nullable();
            $table->string('quantity')->nullable();
            $table->enum('is_show',['show','hidden'])->default('show');
            $table->primary(['sub_category_id', 'shop_id']);
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
        Schema::dropIfExists('shop_sub_category');
    }
}
