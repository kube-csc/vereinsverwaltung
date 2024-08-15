<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lanes', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('regatta_id');
            $table->unsignedBigInteger('rennen_id');
            $table->unsignedBigInteger('tabele_id')->nullable();
            $table->unsignedBigInteger('mannschaft_id')->nullable();
            $table->time('zeit');
            $table->integer('hundert');
            $table->integer('punkte');
            $table->integer('platz');
            $table->integer('rennenvor_id')->nullable();;
            $table->integer('tabelevor_id')->nullable();;
            $table->integer('platzvor')->nullable();;
            $table->integer('bahn');

            $table->unsignedBigInteger('autor_id');
            $table->unsignedBigInteger('bearbeiter_id');

            $table->SoftDeletes();
            $table->timestamps();

            $table->foreign('tabele_id')
                ->references('id')->on('tabeles');

            $table->foreign('mannschaft_id')
                ->references('id')->on('regatta_teams');

         /*
            $table->foreign('renner_id')
                ->references('id')->on('renners');
            */
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lanes', function (Blueprint $table) {
            $table->dropForeign(['mannschaft_id']);
            $table->dropForeign(['tabele_id']);
        });
        Schema::dropIfExists('lanes');
    }
};
