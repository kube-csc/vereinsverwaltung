<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('race_types', function (Blueprint $table) {
            $table->integer('max_trainingstermine')->default(0)->after('training');
            $table->decimal('training_preis', 8, 2)->default(0)->after('max_trainingstermine');
        });
    }

    public function down(): void
    {
        Schema::table('race_types', function (Blueprint $table) {
            $table->dropColumn('max_trainingstermine');
            $table->dropColumn('training_preis');
        });
    }
};
