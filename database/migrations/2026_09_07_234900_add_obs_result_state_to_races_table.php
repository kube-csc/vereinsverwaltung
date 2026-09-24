<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('races', function (Blueprint $table) {
            $table->boolean('obs_results_displayed')->default(false)->after('liveStreamURL')->comment('Ergebnisse in OBS angezeigt');
            // = 0 => Ergebnisse noch nicht in OBS angezeigt,
            //    1 => Ergebnisse wurden in OBS angezeigt
            //    2 => Ergebnisse wurde korrigiert und soll  in OBS angezeigt werden
        });
    }

    public function down(): void
    {
        Schema::table('races', function (Blueprint $table) {
               $table->dropColumn('obs_results_displayed');
        });
    }
};
