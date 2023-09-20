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
        Schema::table('assign_to_deliveries', function (Blueprint $table) {
            $table->foreign(['delivery_boy_id'])->references(['id'])->on('delivery_boys');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('assign_to_deliveries', function (Blueprint $table) {
            $table->dropForeign('assign_to_deliveries_delivery_boy_id_foreign');
        });
    }
};
