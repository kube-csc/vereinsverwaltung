<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('role_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['role_id', 'user_id']);
        });

        // Legacy-Migration: Alle Benutzer mit users.admin = 4 erhalten die Administrator-Rolle.
        $adminRoleId = DB::table('roles')->where('name', 'administrator')->value('id');

        if ($adminRoleId) {
            $now = now();
            DB::table('users')
                ->where('admin', 4)
                ->pluck('id')
                ->each(function ($userId) use ($adminRoleId, $now) {
                    DB::table('role_user')->insertOrIgnore([
                        'role_id' => $adminRoleId,
                        'user_id' => $userId,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('role_user');
    }
};
