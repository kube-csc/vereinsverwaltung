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
            $table->string('abteilung','40');
            $table->unsignedBigInteger('event_id');
            $table->unsignedBigInteger('sportSections_id');
            $table->integer('status');
            $table->string('bild')->nullable();
            $table->string('domain')->nullable();
            $table->string('farbe')->nullable();
            $table->unsignedBigInteger('user_id');
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
        Schema::dropIfExists('sport_sections');
    }
}
