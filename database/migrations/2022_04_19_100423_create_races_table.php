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
            $table->integer('event_id');
            $table->integer('tabele_id')->nullable();
            $table->integer('tabelerennen_id')->nullable();
            $table->integer('gruppe_id')->nullable();
            $table->date('datumvon');
            $table->time('uhrzeit');
            $table->time('verspaetungUhrzeit')->default('00:00:00');
            $table->string('rennBezeichnung', 50);
            $table->text('beschreibung')->nullable();
            $table->text('ergebnisBeschreibung')->nullable();
            $table->integer('bahnen')->nullable();
            $table->string('nummer', 25)->nullable();
            $table->integer('level')->default(0);
            $table->integer('status')->default(0);
            //$table->binary('nachtermin', 65535);
            //$table->binary('anmeldetext', 65535);
            //$table->integer('onlinemeldung')->default(0);
            //$table->date('erstelldatum');
            $table->string('bild')->nullable();
            $table->integer('pixx')->nullable();
            $table->integer('pixy')->nullable();
            $table->string('programmDatei')->nullable();
            $table->string('ergebnisDatei')->nullable();
            $table->integer('mix')->default(0);
            $table->unsignedBigInteger('autor_id')->nullable();
            $table->unsignedBigInteger('bearbeiter_id')->nullable();
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
