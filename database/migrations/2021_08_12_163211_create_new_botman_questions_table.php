<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewBotmanQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('new_botman_questions', function (Blueprint $table) {
            $table->id();
            $table->string('question');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('botmanuser_id')->nullable();
            $table->string('chatUserName')->nullable();
            $table->boolean('visible')->default(true);  // true = 1 = sichtbar
            $table->SoftDeletes();
            $table->timestamps();

            $table->foreign('user_id')
                  ->references('id')->on('users');
            //    ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('new_botman_questions', function (Blueprint $table) {
            $table->dropForeign('user_id');
        });
        Schema::dropIfExists('new_botman_questions');
    }
}
