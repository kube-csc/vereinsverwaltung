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
        Schema::create('coursedates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_id');
            $table->unsignedBigInteger('organiser_id');

            $table->time('kurslaenge');
            $table->dateTime('kursstarttermin');
            $table->dateTime('kursendtermin');
            $table->dateTime('kursstartvorschlag');
            $table->dateTime('kursendvorschlag');
            $table->dateTime('kursstartvorschlagkunde');
            $table->dateTime('kursendvorschlagkunde');
            $table->boolean('kursNichtDurchfuerbar')->default(false);
            $table->decimal('kursKosten',8, 2)->nullable();
            $table->string('kursBezahlsystem')->nullable();

            $table->integer('sportgeraetanzahl');
            $table->float('kursFahrtenlaenge')->default(0);
            $table->text('kursInformation')->nullable();

            $table->unsignedBigInteger('bearbeiter_id');
            $table->unsignedBigInteger('autor_id');

            $table->SoftDeletes();
            $table->timestamps();

            $table->foreign('course_id')->references('id')->on('courses');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coursedates', function (Blueprint $table) {
            $table->dropForeign(['course_id']);
        });
        Schema::dropIfExists('coursedates');
    }
};
