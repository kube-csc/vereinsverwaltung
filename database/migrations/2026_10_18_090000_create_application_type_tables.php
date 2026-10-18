<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('application_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('beschreibung')->nullable();
            $table->date('frist')->nullable();
            $table->boolean('aktiv')->default(true);
            $table->timestamps();
        });

        Schema::create('application_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_type_id')->constrained()->restrictOnDelete();
            $table->string('key');
            $table->string('label');
            $table->string('type', 30);
            $table->boolean('required')->default(false);
            $table->json('options')->nullable();
            $table->text('help_text')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('aktiv')->default(true);
            $table->timestamps();
            $table->unique(['application_type_id', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_fields');
        Schema::dropIfExists('application_types');
    }
};
