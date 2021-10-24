<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBoardUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('board_users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('board_id');
            $table->unsignedBigInteger('boardUser_id')->nullable();
            $table->integer('position');
            $table->integer('nummer')->nullable();;
            $table->string('postenemail')->nullable();
            $table->string('postenbild')->nullable();
            $table->boolean('visible')->default(true);  // true = 1 = sichtbar
            $table->unsignedBigInteger('bearbeiter_id');
            $table->timestamps();

            $table->foreign('boardUser_id')
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
        Schema::table('board_users', function (Blueprint $table) {
            $table->dropForeign('boardUser_id');
            $table->dropForeign('board_id');
        });

        Schema::dropIfExists('board_users');
    }
}
