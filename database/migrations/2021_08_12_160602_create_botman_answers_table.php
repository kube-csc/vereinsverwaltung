<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBotmanAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('botman_answers', function (Blueprint $table) {
            $table->id();
            $table->string('answer');
            $table->string('link')->nullable();
            $table->unsignedBigInteger('bild_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->SoftDeletes();
            $table->timestamps();

            $table->foreign('user_id')
                  ->references('id')->on('users');
                //->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('botman_answers');
    }
}
