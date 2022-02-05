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
            $table->boolean('visible')->default(true);      // true = 1 = sichtbar
            $table->integer('teilUrl')->default(0);         // = 1 = Teilurl Abgeschitten am ?
                                                                         // = 2 Teilurl Abgeschitten am ? mit Verwendung des ersten Parameter
                                                                         // = 3 Parameter austauschen

                                                                         // = 4 ein Parameter verhinder das neu aufnehmen in der Datenbank
            $table->text('prefixName')->nullable();
            $table->unsignedBigInteger('bearbeiter_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamp('nichtgefundenDatum')->nullable();
            $table->integer('nichtgefundenAnzahl')->nullable();
            $table->timestamp('weiterleitDatum')->nullable();
            $table->integer('weiterleitAnzahl')->nullable();
            $table->SoftDeletes();
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
