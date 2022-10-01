<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClubsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clubs', function (Blueprint $table) {
            $table->id();
            $table->string('clubname', 50);
            $table->string('clubemail')->nullable();
            $table->string('clubtelefon')->nullable();
            $table->string('clubhomepage')->nullable();
            $table->string('clublogofile')->nullable();
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
        Schema::dropIfExists('clubs');
    }
}
