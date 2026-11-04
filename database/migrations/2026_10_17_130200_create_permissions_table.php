<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->string('label', 150);
            $table->timestamps();
        });

        $now = now();
        $permissions = [
            'antraege.foerdermittel_verwalten' => 'Fördermittel und Formulare verwalten',
            'antraege.benutzer' => 'Benutzer einladen und begrenzte Rollen vergeben',
            'antraege.pruefen' => 'Anträge einsehen und bewerten',
            'antraege.entscheiden' => 'Förderanträge bewilligen oder ablehnen',
            'antraege.antraege_loeschen' => 'Eingereichte Förderanträge archivieren',
            'antraege.stellen' => 'Eigene Anträge stellen',
        ];
        foreach ($permissions as $name => $label) {
            DB::table('permissions')->insert([
                'name' => $name,
                'label' => $label,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
