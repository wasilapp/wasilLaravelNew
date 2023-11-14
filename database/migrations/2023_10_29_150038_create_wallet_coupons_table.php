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
        Schema::create('wallet_coupons', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('user_id')->constrained('users', 'id')->onDelete('cascade');
            $table->foreignId('wallet_id')->constrained('wallets', 'id')->onDelete('cascade');
            $table->double('usage')->default(0);
            $table->double('price')->default(0);
            $table->boolean('status')->default(true);
            $table->boolean('is_paid')->default(true);
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
        Schema::dropIfExists('wallet_coupons');
    }
};
