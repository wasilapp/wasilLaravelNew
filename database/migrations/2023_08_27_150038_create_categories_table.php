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
        Schema::create('categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title')->unique();
            $table->longText('description')->nullable();
            $table->double('commesion')->nullable();
            $table->string('image_url')->nullable();
            $table->string('type', 250);
            $table->double('delivery_fee', 8, 2)->default(0);
            $table->double('expedited_fees')->nullable();
            $table->double('scheduler_fees')->nullable();
            $table->time('start_work_time')->default('10:00');
            $table->time('end_work_time')->default('20:00');
            $table->boolean('active')->default(true);
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
        Schema::dropIfExists('categories');
    }
};
