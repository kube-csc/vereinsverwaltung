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
        Schema::create('organiserinformations', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('organiser_id');

            $table->text('veranstaltungBeschreibungLang')->nullable();
            $table->text('veranstaltungBeschreibungKurz')->nullable();
            $table->text('sportartBeschreibungLang')->nullable();
            $table->text('sportartBeschreibungKurz')->nullable();
            $table->text('materialBeschreibungLang')->nullable();
            $table->text('materialBeschreibungKurz')->nullable();
            $table->text('keineKurse')->nullable();
            $table->text('terminInformation')->nullable();
            $table->text('mitgliedschaftKurz')->nullable();
            $table->text('mitgliedschaftLang')->nullable();

            $table->unsignedBigInteger('bearbeiter_id');
            $table->unsignedBigInteger('autor_id');
            $table->unsignedBigInteger('freigeber_id')->nullable();
            $table->timestamp('letzteFreigabe')->nullable();

            $table->SoftDeletes();
            $table->timestamps();

            $table->foreign('organiser_id')->references('id')->on('organisers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organiserinformations', function (Blueprint $table) {
            $table->dropForeign(['organiser_id']);
        });
        Schema::dropIfExists('organiserinformations');
    }
};
