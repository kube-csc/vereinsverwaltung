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
        Schema::create('raffle_organizations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_id');
            $table->string('name');
            $table->unsignedBigInteger('rubrik_team_id')->nullable();
            $table->json('criteria')->nullable(); // Speichert Verein, PLZ, Ort als Kriterien
            $table->timestamps();

            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            $table->foreign('rubrik_team_id')->references('id')->on('regatta_teams')->onDelete('set null');
        });

        Schema::table('regatta_teams', function (Blueprint $table) {
            $table->unsignedBigInteger('raffle_organization_id')->nullable()->after('teamlink');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('regatta_teams', function (Blueprint $table) {
            $table->dropColumn('raffle_organization_id');
        });
        Schema::dropIfExists('raffle_organizations');
    }
};
