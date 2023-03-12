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
        Schema::create('timetabel_helper_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('operational_location_id')->constrained()->cascadeOnDelete();
            $table->date('datum');
            $table->time('startZeit');
            $table->time('endZeit');
            $table->time('laenge');
            $table->integer('anzahlHelfer');
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
        Schema::dropIfExists('timetabel_helper_lists');
    }
};


/**
 * Export to PHP Array plugin for PHPMyAdmin
 * @version 5.2.0
 */

/**
 * Database `helferplaner`
 */

/* `helferplaner`.`operational_locations` */

