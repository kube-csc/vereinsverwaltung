<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSliteShowResultToRacesTable extends Migration
{
    public function up()
    {
        Schema::table('races', function (Blueprint $table) {
            if (!Schema::hasColumn('races', 'sliteShowResult')) {
                $table->boolean('sliteShowResult')->default(false)->after('aktuellLiveVideo')->comment('Slideshow-Ergebnis aktiv');
            }
            if (!Schema::hasColumn('races', 'liveStream')) {
                $table->boolean('liveStream')->default(false)->after('sliteShowResult')->comment('Livestream aktiv');
            }
            if (!Schema::hasColumn('races', 'liveStreamURL')) {
                $table->string('liveStreamURL')->nullable()->after('liveStream')->comment('Livestream URL');
            }
            if (!Schema::hasColumn('races', 'einspielerURL')) {
                $table->string('einspielerURL')->nullable()->after('liveStreamURL')->comment('Einspieler URL');
            }
            if (!Schema::hasColumn('races', 'abspielzeit')) {
                $table->integer('abspielzeit')->nullable()->after('einspielerURL')->comment('Abspielzeit in Sekunden');
            }
        });
    }

    public function down()
    {
        Schema::table('races', function (Blueprint $table) {
            if (Schema::hasColumn('races', 'sliteShowResult')) {
                $table->dropColumn('sliteShowResult');
            }
            if (Schema::hasColumn('races', 'liveStream')) {
                $table->dropColumn('liveStream');
            }
            if (Schema::hasColumn('races', 'liveStreamURL')) {
                $table->dropColumn('liveStreamURL');
            }
            if (Schema::hasColumn('races', 'einspielerURL')) {
                $table->dropColumn('einspielerURL');
            }
            if (Schema::hasColumn('races', 'abspielzeit')) {
                $table->dropColumn('abspielzeit');
            }
        });
    }
}
