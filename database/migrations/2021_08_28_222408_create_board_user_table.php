<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBoardUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('board_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('board_id');
            $table->integer('position');
            $table->integer('nummer');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->foreign('user_id')
              ->references('id')->on('users');

            $table->foreign('board_id')
                ->references('id')->on('boards');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('board_user');
    }
}
