<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegattaInformationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('regatta_information', function (Blueprint $table) {
            $table->id();
            $table->integer('event_id');
            $table->integer('position');
            $table->boolean('visible')->default(true);  // true = 1 = sichtbar
            $table->string('informationTittel', 50);
            $table->text('informationBeschreibung')->nullable();
            $table->timestamp('startDatum')->nullable();
            $table->timestamp('startDatumVerschoben')->nullable();
            $table->boolean('startDatumAktiv')->default(true); // true = 1 = Startzeit wird in der Abfrage berücksichtigt
            $table->timestamp('endDatum')->nullable();
            $table->timestamp('endDatumVerschoben')->nullable();
            $table->boolean('endDatumAktiv')->default(true);   // true = 1 = Endzeit wird in der Abfrage berücksichtigt
            $table->unsignedBigInteger('freigeber_id')->nullable();
            $table->timestamp('letzteFreigabe')->nullable();
            $table->unsignedBigInteger('bearbeiter_id');
            $table->unsignedBigInteger('user_id');
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
        Schema::dropIfExists('regatta_information');
    }
}
