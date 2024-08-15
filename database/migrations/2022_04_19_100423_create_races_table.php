<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRacesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('races', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('event_id');
            $table->unsignedBigInteger('tabele_id')->nullable();
            $table->unsignedBigInteger('tabelerennen_id')->nullable();
            $table->unsignedBigInteger('gruppe_id')->nullable();
            $table->date('rennDatum');
            $table->time('rennUhrzeit');
            $table->time('verspaetungUhrzeit');
            $table->time('veroeffentlichungUhrzeit');
            $table->boolean('visible')->default(true);  // true = 1 = sichtbar
            $table->string('rennBezeichnung', 50);
            $table->text('beschreibung')->nullable();
            $table->text('ergebnisBeschreibung')->nullable();
            $table->integer('bahnen')->nullable();
            $table->string('nummer', 25)->nullable();
            $table->integer('level')->default(0);
            $table->integer('mix')->default(0);
            $table->integer('status')->default(0);   // Rennstatus
                                                                    // 0 => keine Mannschaften gesetzt
                                                                    // 1 => Mannschaften gesetzt aber noch nicht geprüft
                                                                    // 2 => Mannschaften gesetzt und geprüft
                                                                    // 3 => Rennn-Ergebnisse eingetragen
                                                                    // 4 => Rennn-Ergebnisse geprüft
                                                                    // 5 =>
                                                                    // 6 =>

            $table->string('bild')->nullable();
            $table->string('fileBild')->nullable();
            $table->integer('pixx')->nullable();
            $table->integer('pixy')->nullable();
            $table->string('programmDatei')->nullable();
            $table->string('fileProgrammDatei')->nullable();
            $table->string('ergebnisDatei')->nullable();
            $table->string('fileErgebnisDatei')->nullable();

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
        Schema::dropIfExists('races');
    }
}
