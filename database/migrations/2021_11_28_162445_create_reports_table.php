<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->integer('event_id');
            $table->integer('position')->default(10);
            $table->string('titel', 45);
            $table->text('kommentar')->nullable();
            $table->string('bild', 100)->nullable();
            $table->integer('pixx')->nullable();
            $table->integer('pixy')->nullable();
            $table->string('filename')->nullable();
            $table->integer('player')->nullable();
            $table->integer('typ')->default(1);             //        1 = jpg
                                                                         //        2 = gif
                                                                         //        3 = png
                                                                         //       10 = pdf
            $table->boolean('visible')->default(true);      // true = 1 = sichtbar
            $table->boolean('startseite')->default(false);  // true = 1 = Leandingpage
            $table->unsignedBigInteger('bearbeiter_id');
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
        Schema::dropIfExists('reports');
    }
}
