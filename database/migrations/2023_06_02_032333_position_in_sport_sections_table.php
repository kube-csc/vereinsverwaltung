<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PositionInSportSectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sport_sections', function (Blueprint $table) {
            $table->integer('position')->default('10')->after('sportSection_id');
            $table->string('filename')->nullable()->after('bild');
            $table->string('keywords')->nullable()->after('farbe');
            $table->string('description')->nullable()->after('keywords');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sport_sections', function (Blueprint $table) {
            $table->dropColumn('position');
            $table->dropColumn('filename');
            $table->dropColumn('keywords');
            $table->dropColumn('description');
        });
    }
}
