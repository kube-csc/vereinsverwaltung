<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTabelesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tabeles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_id')->nullable();
            $table->unsignedBigInteger('gruppe_id')->nullable();
            $table->unsignedBigInteger('system_id')->nullable();
            $table->date('tabelleDatumVon');
            $table->string('ueberschrift', 50)->default('');
            $table->binary('beschreibung', 65535)->nullable();
            $table->integer('tabelleLevelVon');
            $table->integer('tabelleLevelBis');
            $table->integer('wertungsart')->default(0);  //1 = Punkte
                                                                      //2 = Zeit
                                                                      //3 = Lauf
            $table->integer('status')->default(0);
            $table->boolean('tabelleVisible')->default(true);  // true = 1 = sichtbar
            $table->integer('finale')->default(0);
            $table->integer('getrenntewertung')->default(0);
            $table->integer('punktegleich')->default(0);
            $table->integer('punktegleichlauf')->default(0);
            $table->string('tabelleDatei')->nullable();
            $table->string('fileTabelleDatei')->nullable();
            $table->time('finaleAnzeigen');
            $table->unsignedBigInteger('autor_id');
            $table->unsignedBigInteger('bearbeiter_id');

            $table->SoftDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tabeles');
    }
}
