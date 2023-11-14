<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveryBoySubCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_boy_sub_category', function (Blueprint $table) {
            $table->foreignId('delivery_boy_id');
            $table->foreignId('sub_category_id');
            $table->string('price')->nullable();
            $table->string('total_quantity')->nullable();
            $table->string('available_quantity')->nullable();
          //  $table->primary(['delivery_boy_id', 'sub_category_id']);
            $table->primary(['delivery_boy_id', 'sub_category_id'], 'deliverysubCat');

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
        Schema::dropIfExists('delivery_boy_sub_category');
    }
}
