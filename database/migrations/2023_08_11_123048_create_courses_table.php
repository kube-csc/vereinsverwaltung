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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('organiser_id');

            $table->string('kursName');
            $table->text('kursBeschreibung')->nullable();
            $table->decimal('kursKosten',8, 2)->nullable();
            $table->string('kursBezahlsystem')->nullable();
            $table->boolean('visible')->default(true);  // true = 1 = sichtbar
            $table->boolean('trainer')->default(false);  // true = 1 = Trainer ist Pflicht

            $table->unsignedBigInteger('autor_id');
            $table->unsignedBigInteger('bearbeiter_id');
            $table->unsignedBigInteger('freigeber_id')->nullable();
            $table->timestamp('letzteFreigabe')->nullable();

            $table->SoftDeletes();
            $table->timestamps();

            $table->foreign('organiser_id')->references('id')->on('organisers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Entferne die Foren-Keys
        Schema::table('courses', function (Blueprint $table) {
            $table->dropForeign(['organiser_id']);
        });
        Schema::dropIfExists('courses');
    }
};
