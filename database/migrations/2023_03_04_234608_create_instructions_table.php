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
        if (!Schema::hasTable('instructions')) {
            Schema::create('instructions', function (Blueprint $table) {
                $table->id();
                $table->string('ueberschrift' ,50);
                $table->unsignedBigInteger('event_id')->nullable();
                $table->text('beschreibung')->nullable();
                $table->boolean('hauptmenu');    // true = 1 = Hauptmenuüberschrift
                $table->boolean('visible');      // true = 1 = sichtbar
                $table->integer('hauptmenuspalte');
                $table->boolean('systemmenu');    // true = 1 = Systemmenu
                $table->integer('position');
                $table->text('route')->nullable();
                $table->unsignedBigInteger('freigeber_id')->nullable();
                $table->unsignedBigInteger('bearbeiter_id');
                $table->unsignedBigInteger('user_id');
                $table->timestamp('letzteFreigabe')->nullable();
                $table->SoftDeletes();
                $table->timestamps();
            });
        }
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
};
