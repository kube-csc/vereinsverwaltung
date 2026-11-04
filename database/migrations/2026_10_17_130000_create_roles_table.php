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

        $now = now();
        DB::table('roles')->insertOrIgnore([
            ['name' => 'administrator', 'label' => 'Administrator', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'foerdertopfverwalter', 'label' => 'Fördertopfverwalter', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'gremium', 'label' => 'Gremium', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'antragsteller', 'label' => 'Antragsteller', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
