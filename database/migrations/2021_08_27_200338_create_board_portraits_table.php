<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBoardPortraitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('board_portraits', function (Blueprint $table){
            $table->id();
            $table->string('postenPortraet_id');
            $table->string('postenPortraet');
            $table->date('datumvon');
            $table->date('datumbis');
            $table->boolean('visible')->default(true);  // true = 1 = sichtbar
            $table->unsignedBigInteger('bearbeiter_id');
            $table->unsignedBigInteger('user_id');
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
        Schema::dropIfExists('board_portraits');
    }
}
