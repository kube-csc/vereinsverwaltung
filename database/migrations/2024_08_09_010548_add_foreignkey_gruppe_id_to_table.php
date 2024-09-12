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
            $table->foreign('gruppe_id')
                ->references('id')->on('race_types');

            // $table->renameColumn('finaleAnzeigen', 'veroeffentlichungUhrzeit'); ToDo: Automatische Umstellung funktioniert nicht
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
            // $table->renameColumn('veroeffentlichungUhrzeit', 'finaleAnzeigen'); ToDo: Automatische Umstellung funktioniert nicht
                                                                                   // Information im Update anpassen
            $table->dropForeign(['gruppe_id']);
        });
    }
};
