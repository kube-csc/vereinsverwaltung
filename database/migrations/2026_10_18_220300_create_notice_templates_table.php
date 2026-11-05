<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notice_templates', function (Blueprint $table) {
            $table->id();
            $table->string('type', 30)->unique(); // bewilligt | abgelehnt | endabrechnung
            $table->string('titel');
            $table->text('text');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notice_templates');
    }
};
