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
        Schema::create('course_sport_section', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('course_id');
            $table->unsignedBigInteger('sport_section_id');

            $table->timestamps();

            $table->foreign('course_id')->references('id')->on('courses');
            $table->foreign('sport_section_id')->references('id')->on('sport_sections');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_sport_section', function (Blueprint $table) {
            $table->dropForeign(['course_id']);
            $table->dropForeign(['sport_section_id']);
        });
        Schema::dropIfExists('course_sport_section');
    }
};
