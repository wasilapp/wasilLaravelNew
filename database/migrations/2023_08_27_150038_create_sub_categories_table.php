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
        Schema::create('sub_categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->longText('description')->nullable();
            $table->double('price');
            $table->unsignedBigInteger('shop_id')->nullable();
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
            $table->unsignedBigInteger('category_id')->index('sub_categories_category_id_foreign');
            $table->boolean('active')->default(true);
            $table->boolean('is_primary')->default(false);
            $table->string('image_url', 250)->nullable();
            $table->tinyInteger('is_approval')->default(0);
            $table->tinyInteger('quantity')->default(50);
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
        Schema::dropIfExists('sub_categories');
    }
};
