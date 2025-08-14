<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSliteShowResultToRacesTable extends Migration
{
    public function up()
    {
        Schema::table('races', function (Blueprint $table) {
            $table->boolean('sliteShowResult')->default(false)->after('aktuellLiveVideo')->comment('Slideshow-Ergebnis aktiv');
        });
    }

    public function down()
    {
        Schema::table('races', function (Blueprint $table) {
            $table->dropColumn('sliteShowResult');
        });
    }
}

