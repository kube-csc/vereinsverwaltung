<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
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
            // Auftraggeberkonto für die Überweisungsdatei (Konto ohne Benutzer in bank_accounts)
            $table->foreignId('bank_account_id')->nullable()->constrained('bank_accounts')->nullOnDelete();
            $table->boolean('geschlossen')->default(false);
            // null = unbegrenzt
            $table->unsignedInteger('max_applications_per_user')->nullable()->default(null);
            $table->unsignedInteger('max_applications_per_club')->nullable()->default(null);
            // Endabrechnung
            $table->timestamp('settled_at')->nullable();
            $table->unsignedBigInteger('settled_by')->nullable();
            $table->decimal('settlement_factor', 10, 6)->nullable();
            $table->decimal('settlement_total', 12, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('funding_pots');
    }
};
