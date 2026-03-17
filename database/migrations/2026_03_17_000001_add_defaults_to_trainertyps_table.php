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
        Schema::table('trainertyps', function (Blueprint $table) {
            $table->integer('default_sichtbar')->default(1)->after('status');
            $table->unsignedBigInteger('default_sportSection_id')->default(0)->after('default_sichtbar');
            $table->unsignedBigInteger('default_organiser_id')->default(0)->after('default_sportSection_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trainertyps', function (Blueprint $table) {
            $table->dropColumn([
                'default_sichtbar',
                'default_sportSection_id',
                'default_organiser_id',
            ]);
        });
    }
};

