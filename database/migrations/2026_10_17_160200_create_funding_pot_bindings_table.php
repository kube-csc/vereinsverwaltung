<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Budgetbindungen; application_id verweist bewusst ohne Fremdschlüssel auf den Antrag
        Schema::create('funding_pot_bindings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('funding_pot_id')->constrained()->restrictOnDelete();
            $table->string('art', 20); // reserviert | bewilligt | freigegeben
            $table->decimal('betrag', 12, 2);
            $table->unsignedBigInteger('application_id')->nullable()->index();
            $table->string('referenz')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('funding_pot_bindings');
    }
};
