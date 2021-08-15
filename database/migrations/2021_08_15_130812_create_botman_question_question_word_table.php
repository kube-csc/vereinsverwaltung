<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBotmanQuestionQuestionWordTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('botman_question_question_word', function (Blueprint $table) {
            $table->unsignedBigInteger('question_id')->nullable();
            $table->unsignedBigInteger('word_id')->nullable();
            $table->timestamps();

            $table->primary(
              [
                'question_id', 'word_id'
              ]);

            $table->foreign('question_id')
                ->references('id')->on('botman_questions')
                ->onDelete('cascade');

            $table->foreign('word_id')
                ->references('id')->on('question_words')
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
        Schema::dropIfExists('botman_question_question_word');
    }
}
