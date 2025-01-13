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
        Schema::create('course_participant_bookeds', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('trainer_id')->nullable();
            $table->unsignedBigInteger('mitglied_id')->nullable();
            $table->unsignedBigInteger('participant_id')->nullable();
            $table->unsignedBigInteger('regattaTeam_id')->nullable();
            $table->unsignedBigInteger('kurs_id');

            $table->float('teilnehmerFahrtenlaenge')->default(0);
            $table->text('teilnehmerInformation')->nullable();

            $table->foreign('trainer_id')->references('id')->on('users');
            $table->foreign('participant_id')->references('id')->on('course_participants');
            $table->foreign('kurs_id')->references('id')->on('coursedates')->onDelete('cascade');

            $table->SoftDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_participant_bookeds', function (Blueprint $table) {
            $table->dropForeign(['course_participant_id']);
            $table->dropForeign(['kurs_id']);
        });
        Schema::dropIfExists('course_participant_bookeds');
    }
};
