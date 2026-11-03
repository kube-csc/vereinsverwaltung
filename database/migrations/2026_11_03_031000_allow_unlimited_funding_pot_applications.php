<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('funding_pots')
            ->where('max_applications_per_user', 1)
            ->update(['max_applications_per_user' => null]);

        DB::table('funding_pots')
            ->where('max_applications_per_club', 1)
            ->update(['max_applications_per_club' => null]);

        Schema::table('funding_pots', function (Blueprint $table) {
            $table->unsignedInteger('max_applications_per_user')->nullable()->default(null)->change();
            $table->unsignedInteger('max_applications_per_club')->nullable()->default(null)->change();
        });
    }

    public function down(): void
    {
        DB::table('funding_pots')
            ->whereNull('max_applications_per_user')
            ->update(['max_applications_per_user' => 1]);
        DB::table('funding_pots')
            ->whereNull('max_applications_per_club')
            ->update(['max_applications_per_club' => 1]);

        Schema::table('funding_pots', function (Blueprint $table) {
            $table->unsignedInteger('max_applications_per_user')->default(1)->nullable(false)->change();
            $table->unsignedInteger('max_applications_per_club')->default(1)->nullable(false)->change();
        });
    }
};
