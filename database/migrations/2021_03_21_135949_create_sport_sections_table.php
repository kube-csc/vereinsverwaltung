<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSportSectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sport_sections', function (Blueprint $table) {
            $table->id();
            $table->string('abteilung','45');
            $table->string('abteilungTeamBezeichnung','45');
            $table->unsignedBigInteger('event_id')->nullable();
            $table->unsignedBigInteger('sportSection_id')->nullable();
            $table->integer('status');
            $table->string('bild')->nullable();
            $table->string('domain')->nullable();
            $table->string('farbe')->nullable();
            $table->unsignedBigInteger('bearbeiter_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('freigeber_id')->nullable();
            $table->timestamp('letzteFreigabe')->nullable();
            $table->SoftDeletes();
            $table->timestamps();

            $table->foreign('user_id')
              ->references('id')->on('users');

            /*
            $table->foreign('sportSections_id')
                ->references('id')->on('sport_sections');
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
        Schema::dropIfExists('sport_sections');
    }
}
