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
        Schema::table('race_types', function (Blueprint $table) {
            $table->integer('max_wertungsteilnehmer')->default(0)->after('max');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('race_types', function (Blueprint $table) {
            $table->dropColumn('max_wertungsteilnehmer');
        });
    }
};
