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
        Schema::create('player_data', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('player_id');
            $table->boolean('playervisible');
            $table->boolean('visibleLandingpage');
            $table->boolean('visibleEventpage');
            $table->boolean('visiblePlayerpage');
            $table->integer('breite');
            $table->integer('hoehe');

            $table->SoftDeletes();
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
        Schema::dropIfExists('player_data');
    }
};
