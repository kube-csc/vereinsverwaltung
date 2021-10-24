<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBoardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('boards', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sportSection_id');
            $table->integer('position')->default(1);
            $table->unsignedBigInteger('bearbeiter_id');
            $table->unsignedBigInteger('user_id');
            $table->string('postenmaenlich');
            $table->string('postenweiblich');
            $table->string('abteilungsmail')->nullable();
            $table->boolean('visible')->default(true);  // true = 1 = sichtbar
            $table->SoftDeletes();
            $table->timestamps();

            $table->foreign('sportSection_id')
                ->references('id')->on('sport_sections');

            $table->foreign('user_id')
                ->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('boards', function (Blueprint $table) {
            $table->dropForeign('sportSection_id');
            $table->dropForeign('user_id');
        });
        Schema::dropIfExists('boards');
    }
}
