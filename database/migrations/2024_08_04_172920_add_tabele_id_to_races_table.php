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
        Schema::table('races', function (Blueprint $table) {
            // ToDo: Automatische Umstellung funktioniert nicht
            //$table->unsignedBigInteger('tabele_id')->nullable()->change();

            $table->boolean('aktuellLiveVideo')->default('0')->after('fileErgebnisDatei');
            $table->foreign('tabele_id')->references('id')->on('tabeles');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('races', function (Blueprint $table) {
            $table->dropForeign(['tabele_id']);
            $table->dropColumn('aktuellLiveVideo');
        });

        // ToDo: Automatische Umstellung funktioniert nicht
        //$table->integer('tabele_id')->nullable()->change();
    }
};
