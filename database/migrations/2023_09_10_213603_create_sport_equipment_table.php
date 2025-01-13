<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //ToDo: sport_equipment muss  sport_equipments heißen
        Schema::create('sport_equipment', function (Blueprint $table) {
            $table->id();

            $table->string('sportgeraet');
            $table->unsignedBigInteger('sportSection_id');
            $table->string('bild', 100);
            $table->integer('pixx')->default(0);
            $table->integer('pixy')->default(0);
            $table->date('anschafdatum');
            $table->date('verschrottdatum')->nullable();
            $table->integer('sportleranzahl')->default(1);
            $table->float('laenge')->default(0);
            $table->float('breite')->default(0);
            $table->float('hoehe')->default(0);
            $table->float('gewicht')->default(0);
            $table->float('tragkraft')->default(0);
            $table->text('typ')->nullable();
            $table->string('privat', 1);
            $table->unsignedBigInteger('mitgliedprivat_id')->nullable();
            $table->boolean('visible')->default(true);  // true = 1 = sichtbar

            $table->unsignedBigInteger('autor_id');
            $table->unsignedBigInteger('bearbeiter_id');

            $table->SoftDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sport_equipment');
    }
};
