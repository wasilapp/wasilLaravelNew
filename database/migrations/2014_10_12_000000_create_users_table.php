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
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->default('');
            $table->string('email')->default('');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('mobile')->default('');
            $table->boolean('mobile_verified')->default(false);
            $table->string('fcm_token')->nullable();
            $table->string('avatar_url')->nullable();
            $table->boolean('blocked')->default(false);
            $table->boolean('account_type')->default(0);
            $table->boolean('default')->default(false);
            $table->string('referrer')->nullable();
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
        Schema::dropIfExists('users');
    }
};
