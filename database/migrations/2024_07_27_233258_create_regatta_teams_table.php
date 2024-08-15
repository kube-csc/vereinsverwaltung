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
        Schema::create('regatta_teams', function (Blueprint $table) {
            $table->id();

            $table->date('datum');
            $table->string('teamname');
            $table->string('verein');
            $table->string('teamcaptain');
            $table->string('strasse');
            $table->string('plz');
            $table->string('ort');
            $table->string('telefon');
            $table->string('email');
            $table->string('homepage')->nullable();
            $table->string('status');
            $table->boolean('traning');
            $table->unsignedBigInteger('regatta_id');
            $table->unsignedBigInteger('gruppe_id');
            $table->string('passwort');
            $table->string('bild')->nullable();
            $table->integer('pixx')->nullable();
            $table->integer('pixy')->nullable();
            $table->text('beschreibung')->nullable();
            $table->unsignedBigInteger('teamlink');
            $table->text('kommentar')->nullable();
            $table->string('mannschaftsmail')->nullable();
            $table->string('mailen');
            $table->date('mailendatum')->nullable();
            $table->boolean('werbung');

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
        Schema::dropIfExists('regatta_teams');
    }
};
