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
             $table->boolean('getrenntewertung')->default(0);
        });
    }
};
