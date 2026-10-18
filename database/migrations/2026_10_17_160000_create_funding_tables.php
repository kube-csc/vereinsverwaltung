<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('funding_programs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('beschreibung')->nullable();
            $table->boolean('aktiv')->default(true);
            $table->timestamps();
        });

        Schema::create('funding_pots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('funding_program_id')->constrained()->restrictOnDelete();
            $table->string('name');
            $table->text('beschreibung')->nullable();
            $table->unsignedSmallInteger('jahr')->nullable();
            $table->string('laufzeit', 20)->default('befristet'); // befristet | fortlaufend
            $table->date('beginn')->nullable();
            $table->date('ende')->nullable();
            $table->decimal('budget', 12, 2)->default(0);
            $table->boolean('geschlossen')->default(false);
            $table->unsignedInteger('max_applications_per_user')->default(1);
            $table->unsignedInteger('max_applications_per_club')->nullable();
            $table->timestamps();
        });

        // Budgetbindungen; application_id wird mit der Antragstellung (Schritt 9) verknüpft
        Schema::create('funding_pot_bindings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('funding_pot_id')->constrained()->restrictOnDelete();
            $table->string('art', 20); // reserviert | bewilligt
            $table->decimal('betrag', 12, 2);
            $table->unsignedBigInteger('application_id')->nullable()->index();
            $table->string('referenz')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('funding_pot_bindings');
        Schema::dropIfExists('funding_pots');
        Schema::dropIfExists('funding_programs');
    }
};
