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
        Schema::create('raffle_plans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_id');
            $table->string('version_name')->default('Standard');
            $table->time('start_time')->default('09:00');
            $table->integer('interval')->default(10);
            $table->integer('min_pause')->default(30);
            $table->integer('final_pause')->default(30);
            $table->time('final_start_time')->default('14:00');
            $table->time('award_ceremony_time')->default('18:00');
            $table->integer('min_award_pause')->default(30);
            $table->integer('heat_count')->default(3);
            $table->text('params')->nullable();
            $table->boolean('is_active')->default(false);
            $table->boolean('is_draft')->default(false);
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();

            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('raffle_plans');
    }
};
