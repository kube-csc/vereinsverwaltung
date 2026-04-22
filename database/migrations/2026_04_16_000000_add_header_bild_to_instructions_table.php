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
        Schema::table('instructions', function (Blueprint $table) {
                 // Optionales Headerbild für Informationsseiten.
                // Speichert den relativen Pfad auf dem Disk "public" (z.B. "instructionHeader/xyz.jpg").
                $table->string('headerBild')->nullable()->after('beschreibung');
                $table->string('headerTitel')->nullable()->after('headerBild');
                $table->string('headerSlogen')->nullable()->after('headerTitel');
                $table->string('accentColor', 9)->nullable()->after('headerSlogen');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('instructions', function (Blueprint $table) {
                $table->dropColumn('headerBild');
                $table->dropColumn('headerSlogen');
                $table->dropColumn('headerTitel');
                $table->dropColumn('accentColor');
        });
    }
};

