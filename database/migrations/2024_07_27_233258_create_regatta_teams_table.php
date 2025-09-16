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
        Schema::create('regatta_teams', function (Blueprint $table) {
            $table->id();

            $table->date('datum');
            $table->string('teamname');
            $table->string('verein');
            $table->string('teamcaptain');
            $table->string('strasse');
            $table->string('plz');
            $table->string('ort');
            $table->string('telefon');
            $table->string('email');
            $table->string('homepage')->nullable();

            /**
             * Feld 'status' - Status der Team-Meldung.
             * Mögliche Werte:
             * - Neuanmeldung: Aktiv gemeldetes Team
             * - Warteliste: Team steht auf der Warteliste
             * - Gelöscht: Team wurde gelöscht
             * - Abgemeldet: Team hat sich abgemeldet
             * - Nicht angetreten: Team ist nicht angetreten
             * - Disqualifiziert: Team wurde disqualifiziert
             * - Ausgeschieden: Team ist ausgeschieden
             */
            $table->string('status');
            $table->integer('training')->default(0);
            $table->unsignedBigInteger('regatta_id');
            $table->unsignedBigInteger('gruppe_id');
            $table->string('passwort');
            $table->string('bild')->nullable();
            $table->integer('pixx')->nullable();
            $table->integer('pixy')->nullable();
            $table->text('beschreibung')->nullable();
            $table->unsignedBigInteger('teamlink');
            $table->text('kommentar')->nullable();
            $table->string('mannschaftsmail')->nullable();
            $table->string('mailen');
            $table->date('mailendatum')->nullable();
            /**
             * Feld 'werbung' - Wie wurde das Team auf die Regatta aufmerksam?
             * Mögliche Werte:
             * 0: nicht ausgewählt
             * 1: kel-datteln.de Homepage
             * 2: Day of Dragons Homepage
             * 3: Kanucup-Datteln Homepage
             * 4: Plakatwerbung
             * 5: Flyer
             * 6: Empfehlung von Sportfreunden
             * 7: Radio
             * 8: Drachenboot-Liga
             * 9: Einladungsmail
             * 10: Presse
             * 11: Sonstiges
             * 12: dragonboat.online
             * 13: lokalkompass.de
             */
            $table->string('werbung')->nullable()->comment('Wie wurde das Team auf die Regatta aufmerksam? Siehe Migrationsdatei für mögliche Werte.');

            $table->SoftDeletes();
            $table->timestamps();

            $table->foreign('gruppe_id')
                ->references('id')->on('race_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('regatta_teams', function (Blueprint $table) {
            $table->dropForeign(['gruppe_id']);
        });

        Schema::dropIfExists('regatta_teams');
    }
};
