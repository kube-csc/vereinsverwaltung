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
        Schema::create('trainings', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('sportSection_id');
            $table->unsignedBigInteger('organiser_id');
            $table->unsignedBigInteger('course_id');

            $table->date('datumvon');
            $table->date('datumbis');
            $table->date('datumAktuell');
            $table->time('zeitvon');
            $table->time('zeitbis');
            $table->text('vorschauTage');
            $table->string('wiederholung');
            $table->integer('sportgeraeteanzahl');
            $table->integer('sportgeraeteGebucht');

            $table->unsignedBigInteger('autor_id');
            $table->unsignedBigInteger('bearbeiter_id');

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
        Schema::dropIfExists('trainings');
    }
};
