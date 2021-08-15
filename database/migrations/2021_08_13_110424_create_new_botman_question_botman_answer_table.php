<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewBotmanQuestionBotmanAnswerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('new_botman_question_botman_answer', function (Blueprint $table) {
            $table->unsignedBigInteger('newQuestion_id')->nullable();
            $table->unsignedBigInteger('question_id')->nullable();
            $table->timestamps();
/*
            $table->primary(
              [
                'newBotmanQuestion_id', 'botmanQuestion_id'
              ]);
*/
            $table->foreign('newQuestion_id')
                ->references('id')->on('new_botman_questions')
                ->onDelete('cascade');

            $table->foreign('question_id')
                ->references('id')->on('botman_questions')
                ->onDelete('cascade');

          });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('new_botman_question_botman_answer');
    }
}
