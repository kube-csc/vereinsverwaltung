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
        Schema::create('tabledatas', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('regatta_id');
            $table->unsignedBigInteger('tabele_id');
            $table->unsignedBigInteger('mannschaft_id');

            $table->time('zeit')->default('00:00:00');
            $table->integer('hundert')->default(0);

            $table->integer('punkte')->default(0);
            $table->time('zeitpunktegleich')->default('00:00:00');
            $table->integer('hundertpunktegleich')->default(0);

            $table->integer('buchholzzahl')->default(0);

            $table->integer('rennanzahl')->default(0);

            $table->unsignedBigInteger('autor_id');
            $table->unsignedBigInteger('bearbeiter_id');

            $table->softDeletes();
            $table->timestamps();

            $table->foreign('tabele_id')->references('id')->on('tabeles');
            $table->foreign('mannschaft_id')->references('id')->on('regatta_teams');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tabeles', function (Blueprint $table) {
            $table->dropForeign(['tabele_id']);
            $table->dropForeign(['manschaft_id']);
        });

        Schema::dropIfExists('tabledatas');
    }
};
