<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
            $table->string('label', 100);
            $table->timestamps();
        });

        Schema::create('role_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['role_id', 'user_id']);
        });

        $now = now();
        DB::table('roles')->insertOrIgnore([
            ['name' => 'administrator', 'label' => 'Administrator', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'foerdertopfverwalter', 'label' => 'Fördertopfverwalter', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'gremium', 'label' => 'Gremium', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'antragsteller', 'label' => 'Antragsteller', 'created_at' => $now, 'updated_at' => $now],
        ]);

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->string('label', 150);
            $table->timestamps();
        });

        Schema::create('permission_role', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['permission_id', 'role_id']);
        });

        $permissions = [
            'antraege.foerdermittel_verwalten' => 'Fördermittel und Formulare verwalten',
            'antraege.benutzer' => 'Benutzer einladen und begrenzte Rollen vergeben',
            'antraege.pruefen' => 'Anträge einsehen und bewerten',
            'antraege.entscheiden' => 'Förderanträge bewilligen oder ablehnen',
            'antraege.antraege_loeschen' => 'Eingereichte Förderanträge archivieren',
            'antraege.stellen' => 'Eigene Anträge stellen',
        ];
        foreach ($permissions as $name => $label) {
            DB::table('permissions')->insert([
                'name' => $name,
                'label' => $label,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $rolePermissions = [
            'foerdertopfverwalter' => [
                'antraege.foerdermittel_verwalten',
                'antraege.benutzer',
                'antraege.antraege_loeschen',
            ],
            'gremium' => [
                'antraege.pruefen',
                'antraege.entscheiden',
                'antraege.antraege_loeschen',
            ],
            'antragsteller' => ['antraege.stellen'],
        ];
        foreach ($rolePermissions as $roleName => $permissionNames) {
            $roleId = DB::table('roles')->where('name', $roleName)->value('id');
            $permissionIds = DB::table('permissions')->whereIn('name', $permissionNames)->pluck('id');
            foreach ($permissionIds as $permissionId) {
                DB::table('permission_role')->insert([
                    'permission_id' => $permissionId,
                    'role_id' => $roleId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('permission_role');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('role_user');
        Schema::dropIfExists('roles');
    }
};
