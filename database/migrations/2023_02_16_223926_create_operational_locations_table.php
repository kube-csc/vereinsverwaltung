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
        Schema::create('operational_locations', function (Blueprint $table) {
            $table->id();
            $table->string('einsatzort', 50);
            $table->text('beschreibung')->nullable();
            $table->unsignedBigInteger('autor_id');
            $table->unsignedBigInteger('bearbeiter_id');
            $table->unsignedBigInteger('freigeber_id')->nullable();
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
        Schema::dropIfExists('operational_locations');
    }
};
