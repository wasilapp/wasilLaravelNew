<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWalletsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('description');
            $table->string('image_url')->nullable();
            $table->integer('usage')->default(0);
            $table->double('price')->default(0);
            $table->boolean('active')->default(true);
            $table->string('statu')->default(0);
            $table->foreignId('shop_id')->constrained('shops', 'id')->onDelete('cascade');
            $table->foreignId('subcategory_id')->constrained('sub_categories')->onDelete('cascade');
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
        Schema::dropIfExists('wallets');
    }
}
