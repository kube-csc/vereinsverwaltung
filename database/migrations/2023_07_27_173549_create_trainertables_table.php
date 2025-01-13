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
        Schema::create('trainertables', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('trainertyp_id');
            $table->unsignedBigInteger('sportSection_id');
            $table->unsignedBigInteger('organiser_id');
            $table->integer('status');         //status = 1 aktiv, 0 inaktiv
            $table->integer('sichtbar');       //sichtbar = 1 sichtbar, 0 unsichtbar

            $table->unsignedSmallInteger('autor_id');
            $table->unsignedSmallInteger('bearbeiter_id');

            $table->SoftDeletes();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('trainertyp_id')->references('id')->on('trainertyps');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Entferne die Foren-Keys
        Schema::table('trainertables', function (Blueprint $table) {
            $table->dropForeign(['trainer_id']);
            $table->dropForeign(['trainertyp_id']);
        });
        Schema::dropIfExists('trainertables');
    }
};
