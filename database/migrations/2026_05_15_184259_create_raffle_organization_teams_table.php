<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('raffle_organization_teams', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organization_id');
            $table->unsignedBigInteger('team_id');
            $table->timestamps();

            $table->foreign('organization_id')->references('id')->on('raffle_organizations')->onDelete('cascade');
            $table->foreign('team_id')->references('id')->on('regatta_teams')->onDelete('cascade');

            $table->unique(['organization_id', 'team_id']);
        });

        // Daten migrieren, falls bereits welche vorhanden sind
        $teams = DB::table('regatta_teams')->whereNotNull('raffle_organization_id')->get();
        foreach ($teams as $team) {
            DB::table('raffle_organization_teams')->insert([
                'organization_id' => $team->raffle_organization_id,
                'team_id' => $team->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        Schema::table('regatta_teams', function (Blueprint $table) {
            $table->dropColumn('raffle_organization_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('regatta_teams', function (Blueprint $table) {
            $table->unsignedBigInteger('raffle_organization_id')->nullable()->after('teamlink');
        });

        // Daten zurück migrieren
        $mappings = DB::table('raffle_organization_teams')->get();
        foreach ($mappings as $mapping) {
            DB::table('regatta_teams')
                ->where('id', $mapping->team_id)
                ->update(['raffle_organization_id' => $mapping->organization_id]);
        }

        Schema::dropIfExists('raffle_organization_teams');
    }
};
