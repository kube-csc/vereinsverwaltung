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
        Schema::create('raffle_plan_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('raffle_plan_id');
            $table->integer('race_number');
            $table->unsignedBigInteger('gruppe_id')->nullable();
            $table->time('time');
            $table->integer('lane')->nullable();
            $table->unsignedBigInteger('team_id')->nullable();
            $table->boolean('is_final')->default(false);
            $table->string('final_type')->nullable();
            $table->string('placeholder_name')->nullable();
            $table->unsignedBigInteger('source_tabele_id')->nullable();
            $table->integer('source_place')->nullable();
            $table->integer('heat_index')->nullable();
            $table->integer('pause_minutes')->nullable();
            $table->integer('conflicts')->default(0);
            $table->text('org_intervals')->nullable();
            $table->timestamps();

            $table->index(['source_tabele_id', 'source_place'], 'rpi_source_table_place_idx');

            $table->foreign('raffle_plan_id')->references('id')->on('raffle_plans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('raffle_plan_items');
    }
};
