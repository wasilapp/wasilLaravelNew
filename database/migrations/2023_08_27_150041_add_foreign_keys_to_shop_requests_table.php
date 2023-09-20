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
        Schema::table('shop_requests', function (Blueprint $table) {
            $table->foreign(['manager_id'])->references(['id'])->on('managers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shop_requests', function (Blueprint $table) {
            $table->dropForeign('shop_requests_manager_id_foreign');
        });
    }
};
