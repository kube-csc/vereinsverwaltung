<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInstructionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('instructions', function (Blueprint $table) {
            $table->id();
            $table->string('ueberschrift');
            $table->text('beschreibung')->nullable();
            $table->boolean('hauptmenu');    // true = 1 = Hauptmenu
            $table->boolean('visible');      // true = 1 = sichtbar
            $table->unsignedBigInteger('freigeber_id');
            $table->timestamp('letzteFreigabe')->nullable();
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
        Schema::dropIfExists('instructions');
    }
}
