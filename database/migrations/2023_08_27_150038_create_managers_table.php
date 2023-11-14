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
        Schema::create('managers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('fcm_token')->nullable();
            $table->string('avatar_url')->nullable();
            $table->string('license')->nullable();
            $table->string('public_email')->nullable();
            $table->string('mobile')->nullable();
            $table->boolean('mobile_verified')->default(false);
            $table->tinyInteger('is_approval')->default(0);
            $table->string('address')->nullable();
            $table->string('referrer')->nullable();
            $table->string('referrer_link')->nullable();
            $table->string('otp')->nullable();
            $table->timestamp('otp_expiration')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('managers');
    }
};
