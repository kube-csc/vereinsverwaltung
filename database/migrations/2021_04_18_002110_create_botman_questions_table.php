<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBotmanQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('botman_questions', function (Blueprint $table) {
            $table->id();
            $table->text('question');
            $table->text('answer')->nullable();
            $table->text('name')->nullable();
            $table->unsignedBigInteger('autor')->nullable();
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
        Schema::dropIfExists('botman_questions');
    }
}
