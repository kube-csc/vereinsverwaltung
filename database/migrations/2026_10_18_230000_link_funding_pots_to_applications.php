<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('application_types', 'requires_funding_pot')) {
            Schema::table('application_types', function (Blueprint $table) {
                $table->boolean('requires_funding_pot')->default(false)->after('requires_club');
            });
        }

        if (! Schema::hasColumn('applications', 'form_snapshot')) {
            Schema::table('applications', function (Blueprint $table) {
                $table->json('form_snapshot')->nullable()->after('bank_account_snapshot');
            });
        }

        if (! Schema::hasColumn('applications', 'funding_pot_id')) {
            Schema::table('applications', function (Blueprint $table) {
                $table->foreignId('funding_pot_id')
                    ->nullable()
                    ->after('application_type_id')
                    ->constrained('funding_pots')
                    ->restrictOnDelete();
            });
        }

        if (! Schema::hasTable('application_type_funding_pot')) {
            Schema::create('application_type_funding_pot', function (Blueprint $table) {
                $table->id();
                $table->foreignId('application_type_id')->constrained()->cascadeOnDelete();
                $table->foreignId('funding_pot_id')->constrained()->restrictOnDelete();
                $table->timestamps();
                $table->unique(['application_type_id', 'funding_pot_id'], 'application_type_funding_pot_unique');
            });
        }

        DB::table('application_types')
            ->whereIn('slug', [
                'foerderung-uebungsleiter',
                'foerderung-sportgeraete',
                'foerderung-jugendprojekt',
                'foerderung-integration-gefluechteter-erwachsener',
                'oeffentlichkeitswirksames-quartiersprojekt',
            ])
            ->update(['requires_funding_pot' => true]);
    }

    public function down(): void
    {
        Schema::dropIfExists('application_type_funding_pot');

        if (Schema::hasColumn('applications', 'funding_pot_id')) {
            Schema::table('applications', function (Blueprint $table) {
                $table->dropConstrainedForeignId('funding_pot_id');
            });
        }

        if (Schema::hasColumn('application_types', 'requires_funding_pot')) {
            Schema::table('application_types', function (Blueprint $table) {
                $table->dropColumn('requires_funding_pot');
            });
        }
    }
};
