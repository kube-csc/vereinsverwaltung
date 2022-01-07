<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBacklinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::create('backlinks', function (Blueprint $table) {
            $table->id();
            $table->text('ip');
            $table->text('backlink');
            $table->text('neueUrl')->nullable();
            $table->dateTime('nichtgefundenDatum')->nullable();
            $table->integer('nichtgefundenAnzahl')->nullable();
            $table->dateTime('weiterleitDatum')->nullable();
            $table->integer('weiterleitAnzahl')->nullable();
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
        Schema::dropIfExists('backlinks');
    }
}
