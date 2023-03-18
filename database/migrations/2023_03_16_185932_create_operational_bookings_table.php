<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('operational_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('operational_location_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('timetabel_helper_lists_id');
            //$table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('Vorname');
            $table->string('Nachname');
            $table->string('email');
            $table->date('datum');
            $table->time('startZeit');
            $table->time('endZeit');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('operational_bookings');
    }
};
