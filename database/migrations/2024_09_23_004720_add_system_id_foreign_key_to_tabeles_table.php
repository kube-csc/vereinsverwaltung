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
        Schema::table('tabeles', function (Blueprint $table) {
            $table->foreign('system_id')->references('id')->on('pointsystems');
            $table->dropColumn('getrenntewertung');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tabeles', function (Blueprint $table) {
            $table->dropForeign(['system_id']);
            $table->boolean('getrenntewertung')->default(0);
        });
    }
};
