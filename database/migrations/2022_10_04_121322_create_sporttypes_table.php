<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSporttypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sporttypes', function (Blueprint $table) {
            $table->id();
            $table->string('sportart', 50);
            $table->text('sportartbeschreibung')->nullable();
            $table->string('sportarthomepage')->nullable();
            $table->string('sportartlogofile')->nullable();
            $table->boolean('visible')->default(true);  // true = 1 = sichtbar
            $table->unsignedBigInteger('freigeber_id')->nullable();
            $table->unsignedBigInteger('bearbeiter_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamp('letzteFreigabe')->nullable();
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
        Schema::dropIfExists('sporttypes');
    }
}
