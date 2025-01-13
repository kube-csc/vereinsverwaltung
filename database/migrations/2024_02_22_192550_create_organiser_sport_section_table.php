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
        Schema::create('organiser_sport_section', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organiser_id');
            $table->unsignedBigInteger('sport_section_id');
            $table->timestamps();

            $table->foreign('organiser_id')->references('id')->on('organisers')->onDelete('cascade');
            $table->foreign('sport_section_id')->references('id')->on('sport_sections')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organiser_sport_section', function (Blueprint $table) {
            $table->dropForeign(['organiser_id']);
            $table->dropForeign(['sport_section_id']);
        });
        Schema::dropIfExists('organiser_sport_section');
    }
};
