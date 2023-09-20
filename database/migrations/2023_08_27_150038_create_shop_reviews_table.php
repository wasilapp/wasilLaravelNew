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
        Schema::create('shop_reviews', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->tinyInteger('rating');
            $table->text('review')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('shop_id')->index('shop_reviews_shop_id_foreign');
            $table->timestamps();

            $table->unique(['user_id', 'shop_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_reviews');
    }
};
