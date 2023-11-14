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
        Schema::create('shops', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('mobile')->nullable();
            $table->string('barcode')->unique();
            $table->double('latitude');
            $table->double('longitude');
            $table->string('address');
            $table->string('image_url')->nullable();
            $table->double('rating')->default(0);
            $table->integer('delivery_range')->default(0);
            $table->integer('total_rating')->default(0);
            $table->integer('default_tax')->nullable();
            $table->boolean('available_for_delivery')->default(true);
            $table->boolean('open')->default(false);
            $table->unsignedBigInteger('manager_id')->nullable();
            $table->unsignedBigInteger('category_id')->nullable()->index('shops_category_id_foreign');
            $table->integer('distance')->default(5000);
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
        Schema::dropIfExists('shops');
    }
};
