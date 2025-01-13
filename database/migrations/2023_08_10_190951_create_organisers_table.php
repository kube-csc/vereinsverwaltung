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
        Schema::create('organisers', function (Blueprint $table) {
            $table->id();

            $table->string('veranstaltung');
            $table->string('veranstaltungDomain')->nullable();
            $table->string('veranstaltungHeader')->nullable();
            $table->string('sportartUeberschrift')->nullable();
            $table->string('materialUeberschrift')->nullable();
            $table->string('trainerUeberschrift')->nullable();
            $table->string('kurseUeberschrift')->nullable();

            $table->unsignedBigInteger('bearbeiter_id');
            $table->unsignedBigInteger('autor_id');

            $table->SoftDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organisers');
    }
};
