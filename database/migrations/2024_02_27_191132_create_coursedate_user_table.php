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
        Schema::create('coursedate_user', function (Blueprint $table) {
            $table->id();

            $table->foreignId('coursedate_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coursedate_user', function (Blueprint $table) {
            $table->dropForeign(['coursedate_id']);
            $table->dropForeign(['user_id']);
        });
        Schema::dropIfExists('coursedate_user');
    }
};
