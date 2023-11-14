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
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            
            $table->unsignedBigInteger('status')->default(1);
            $table->foreign('status')->references('id')->on('status')->onDelete('cascade');

            $table->unsignedBigInteger('category_id')->default(1);
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
           
            $table->double('order');
            $table->double('shop_revenue')->nullable();
            $table->double('admin_revenue')->nullable();
            $table->double('delivery_fee')->nullable();;
            $table->double('total');
            $table->integer('otp');
            $table->double('coupon_discount')->nullable();
            $table->double('latitude')->nullable();
            $table->double('longitude')->nullable();
            $table->unsignedBigInteger('coupon_id')->nullable();
            $table->foreign(['coupon_id'])->references(['id'])->on('coupons')->nullOnDelete();

            $table->unsignedBigInteger('delivery_boy_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();;
            $table->foreign(['user_id'])->references(['id'])->on('users')->nullOnDelete();
            

            $table->unsignedBigInteger('address_id')->nullable();
            $table->unsignedBigInteger('shop_id')->nullable();
            $table->unsignedBigInteger('order_payment_id')->nullable();
            $table->foreign(['order_payment_id'])->references(['id'])->on('order_payments')->nullOnDelete();

            $table->enum('order_type', ['normal', 'urgent', 'scheduled'])->default('normal');
            $table->integer('count');
            $table->boolean('type');
            $table->boolean('is_notification')->default(true);
            $table->boolean('is_paid')->default(false);

            $table->boolean('is_wallet')->default(false);
            $table->unsignedBigInteger('wallet_id')->nullable()->index('orderss_wallet_id_foreign');

            // $table->boolean('night_order')->default(false);
            $table->double('expedited_fees')->nullable();
            $table->text('cancellation_reason')->nullable();
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
        Schema::dropIfExists('orders');
    }
};
