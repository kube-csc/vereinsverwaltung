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
                $table->string('veranstaltung', 50)->default('');   // ToDo: wird nicht mehr benötigt
                $table->string('ueberschrift', 50)->default('');
                $table->text('beschreibung')->nullable();
                $table->string('ansprechparter', 50)->nullable();
                $table->string('telefon', 25)->nullable();
                $table->string('email', 50)->nullable();
                $table->string('homepage')->nullable();
                //$table->unsignedBigInteger('gruppe'); // jetzt sportSection_id
                $table->unsignedBigInteger('sportSection_id')->nullable();
                $table->char('startseite', 1)->default('');
                $table->text('nachtermin')->nullable();
                $table->text('anmeldetext')->nullable();
                $table->char('onlinemeldung', 3)->default('');
                //$table->date('erstelldatum');  //jetzt update_at
                $table->char('regatta')->default('');
                $table->integer('verwendung')->nullable();
                                                                    // 0 = Event / Termin
                                                                    // 1 =
                                                                    // 2 =
                                                                    // 3 =
                                                                    // 4 = Abteilungsbeschreibung
                $table->unsignedBigInteger('eventGroup_id')->nullable();
                $table->text('einverstaendnis')->nullable();
                $table->integer('teilnehmer')->default(0);
                $table->integer('teilnehmermax')->default(0);
                $table->unsignedBigInteger('autor_id')->nullable();
                $table->unsignedBigInteger('bearbeiter_id')->nullable();
                //$table->date('bearbeitungsdatum')->nullable();
                $table->char('freigabe', 1)->default('');
                $table->SoftDeletes();
                $table->timestamps();

                $table->foreign('autor_id')
                  ->references('id')->on('users');

                $table->foreign('bearbeiter_id')
                  ->references('id')->on('users');

                $table->foreign('sportSection_id')
                ->references('id')->on('sport_sections');

                $table->foreign('eventGroup_id')
                  ->references('id')->on('event_groups');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign('autor_id');
            $table->dropForeign('bearbeiter_id');
            $table->dropForeign('sportSection_id');
            $table->dropForeign('eventGroup_id');
        });
        Schema::dropIfExists('events');
    }
}
