<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('faqs', function (Blueprint $table) {
            $table->id();

            // thematische Gruppierung (Meldung/Startgeld/Regeln/Orga/...)
            $table->string('category', 100)->index();

            // Sortierung der Kategorie (wird pro Kategorie identisch gespeichert)
            $table->unsignedInteger('category_sort_order')->default(0)->index();

            $table->string('question');
            // HTML für Absätze/Aufzählungen (wird in Blade bewusst mit {!! !!} gerendert)
            $table->longText('answer_html');

            // Reihenfolge innerhalb einer Kategorie
            $table->unsignedInteger('sort_order')->default(0)->index();

            $table->boolean('is_active')->default(true)->index();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('faqs');
    }
};
