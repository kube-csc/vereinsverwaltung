<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('application_type_funding_pot', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_type_id')->constrained()->cascadeOnDelete();
            $table->foreignId('funding_pot_id')->constrained()->restrictOnDelete();
            $table->timestamps();
            $table->unique(['application_type_id', 'funding_pot_id'], 'application_type_funding_pot_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_type_funding_pot');
    }
};
