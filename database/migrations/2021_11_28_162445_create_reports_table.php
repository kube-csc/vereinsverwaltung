<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->integer('event_id');
            $table->integer('position')->default(10);
            $table->string('titel', 45);
            $table->text('kommentar')->nullable();
            $table->string('ordner', 100)->nullable();
            $table->string('bild', 100)->nullable();
            $table->string('image', 100)->nullable();
            $table->integer('pixx')->nullable();
            $table->integer('pixy')->nullable();
            $table->string('filename')->nullable();
            $table->integer('player')->nullable();
            $table->integer('verwendung')->nullable();                   //        0 = alter Status wird als Bild gewertet
                                                                         //        1 = Bild im Report
                                                                         //        2 = Ausschreibung
                                                                         //        3 = Programm
                                                                         //        4 = Ergebnisse
                                                                         //        5 = Plakat
                                                                         //        6 = Presse
                                                                         //        7 = Player
            $table->integer('typ')->default(1);             //        1 = jpg
                                                                         //        2 = gif
                                                                         //        3 = png
                                                                         //       10 = pdf
                                                                         //       11 = doc
                                                                         //       12 = xls
                                                                         //       13 = xlsx
                                                                         //       20 = wmv
                                                                         //       21 = mp4
            $table->integer('presse')->nullable();                       //        0 = alter Status wird als Presse gewertet
            $table->text('quelle')->nullable();
            $table->timestamp('quellDatum')->nullable();
            $table->boolean('visible')->default(true);      // true = 1 = sichtbar
            $table->boolean('startseite')->default(false);  // true = 1 = Leandingpage
            $table->boolean('webseite')->default(false);    // true = 0 = das Bild wird nur angezeigt wenn der User im internen Bereich angemeldet ist.
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
        Schema::dropIfExists('reports');
    }
}
