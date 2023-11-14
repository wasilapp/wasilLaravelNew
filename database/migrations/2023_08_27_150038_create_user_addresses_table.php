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
        Schema::create('user_addresses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->decimal('longitude', 10, 7);
            $table->decimal('latitude', 10, 7);
            $table->integer('distance')->default(50);
            $table->string('name');
            $table->string('street'); //address
            $table->string('building_number')->nullable(); // address2
            $table->string('city');
            $table->boolean('default')->default(false);
            $table->integer('apartment_num'); //pincode
            $table->boolean('active')->default(true);
            $table->integer('type')->default(2);
            $table->unsignedBigInteger('user_id');
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
        Schema::dropIfExists('user_addresses');
    }
};
