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
        Schema::create('coupons', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code')->unique();
            $table->longText('description');
            $table->integer('offer');
            $table->double('min_order');
            $table->double('max_discount');
            $table->boolean('for_only_one_time')->default(true);
            $table->boolean('for_new_user')->default(false);
            $table->boolean('is_active')->default(true);
            $table->date('started_at')->default('2023-03-17');
            $table->date('expired_at');
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
        Schema::dropIfExists('coupons');
    }
};
