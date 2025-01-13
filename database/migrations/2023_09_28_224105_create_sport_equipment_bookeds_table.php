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
        Schema::create('sport_equipment_bookeds', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('sportgeraet_id')->nullable();
            $table->unsignedBigInteger('sportgeraetPrivat_id')->nullable();
            $table->unsignedBigInteger('kurs_id');

            $table->SoftDeletes();
            $table->timestamps();

            $table->foreign('kurs_id')->references('id')->on('coursedates')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sport_equipment_bookeds', function (Blueprint $table) {
            $table->dropForeign(['kurs_id']);
        });
        Schema::dropIfExists('sport_equipment_bookeds');
    }
};
