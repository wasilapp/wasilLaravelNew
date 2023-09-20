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
        Schema::create('delivery_boys', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email')->nullable()->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('fcm_token')->nullable();
            $table->double('latitude')->nullable();
            $table->double('longitude')->nullable();
            $table->boolean('is_free')->default(true);
            $table->boolean('is_offline')->default(false);
            $table->string('avatar_url')->nullable();
            $table->string('mobile')->nullable();
            $table->boolean('mobile_verified')->default(false);
            $table->double('rating')->default(0);
            $table->integer('total_rating')->default(0);
            $table->integer('category_id');
            $table->unsignedBigInteger('shop_id')->nullable();
            $table->string('car_number', 250);
            $table->rememberToken();
            $table->boolean('is_verified')->default(false);
            $table->string('driving_license', 250)->nullable();
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
        Schema::dropIfExists('delivery_boys');
    }
};
