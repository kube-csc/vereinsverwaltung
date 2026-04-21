<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInstructionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('instructions', function (Blueprint $table) {
            $table->id();

            $table->string('ueberschrift' ,50);
            $table->unsignedBigInteger('event_id')->nullable();
            $table->text('beschreibung')->nullable();
            $table->boolean('hauptmenu');
             /*
                0 = Informationseite ohne Hauptmenü und Untermenü
                1 = Hauptmenü ohne Dropdown
                2 = Hauptmenü mit Dropdown (Untermenü)
                3 = Untermenü vom Dropdown Hauptmenü
            */
            $table->integer('hauptmenuspalte');
            $table->boolean('systemmenu');    // true = 1 = Systemmenü
            $table->integer('position');
            $table->boolean('visible');      // true = 1 = sichtbar
            $table->text('route')->nullable();
            $table->unsignedBigInteger('freigeber_id')->nullable();
            $table->unsignedBigInteger('bearbeiter_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamp('letzteFreigabe')->nullable();
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
        Schema::dropIfExists('instructions');
    }
}
