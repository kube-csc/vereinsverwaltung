<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('application_types', function (Blueprint $table) {
            $table->boolean('requires_club')->default(false)->after('frist');
        });

        DB::table('application_types')
            ->whereIn('slug', [
                'foerderung-uebungsleiter',
                'foerderung-sportgeraete',
                'foerderung-jugendprojekt',
                'foerderung-integration-gefluechteter-erwachsener',
                'oeffentlichkeitswirksames-quartiersprojekt',
            ])
            ->update(['requires_club' => true]);
    }

    public function down(): void
    {
        Schema::table('application_types', function (Blueprint $table) {
            $table->dropColumn('requires_club');
        });
    }
};
