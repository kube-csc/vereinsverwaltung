<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();

            $table->date('datumvon');
      			$table->date('datumbis');
      			$table->date('datumvona')->nullable();
      			$table->date('datumbisa')->nullable();
      			$table->string('veranstaltung', 50)->default('');
      			$table->string('ueberschrift', 50)->default('');
      			$table->text('beschreibung')->nullable();
      			$table->string('Ansprechparten', 50)->default('');
      			$table->string('telefon', 25)->default('');
      			$table->string('email', 50)->default('');
      			$table->string('homepage')->default('');
      			$table->unsignedBigInteger('gruppe');
      			$table->char('startseite', 1)->default('');
      			$table->text('nachtermin')->nullable();
      			$table->text('anmeldetext')->nullable();
      			$table->char('onlinemeldung', 3)->default('');
      			$table->date('erstelldatum')->nullable();
      			$table->char('regatta')->default('');
      			$table->integer('verwendung')->default(0);
      			$table->integer('idterminvor')->default(0);
      			$table->text('einverstaendnis')->nullable();
      			$table->integer('teilnehmer')->default(0);
      			$table->integer('teilnehmermax')->default(0);
      			$table->unsignedBigInteger('autor');
      			$table->unsignedBigInteger('bearbeiter');
      			$table->date('bearbeitungsdatum')->nullable();
      			$table->char('freigabe', 1)->default('');

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
        Schema::dropIfExists('events');
    }
}
