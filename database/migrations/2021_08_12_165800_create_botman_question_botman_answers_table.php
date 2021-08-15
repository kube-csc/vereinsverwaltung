<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBotmanQuestionBotmanAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('botman_question_botman_answers', function (Blueprint $table) {
            $table->unsignedBigInteger('question_id')->nullable();
            $table->unsignedBigInteger('answer_id')->nullable();
            $table->timestamps();

            $table->primary(
              [
                'question_id', 'answer_id'
              ]);

            $table->foreign('question_id')
                ->references('id')->on('botman_questions')
                ->onDelete('cascade');

            $table->foreign('answer_id')
                ->references('id')->on('botman_answers')
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
        Schema::dropIfExists('botman_question_botman_answers');
    }
}
