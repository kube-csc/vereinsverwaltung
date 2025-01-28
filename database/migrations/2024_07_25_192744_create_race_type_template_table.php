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
        Schema::create('race_type_templates', function (Blueprint $table) {
            $table->id();

            $table->string('typ');
            $table->integer('altervon')->default(0);
            $table->integer('alterbis')->default(0);
            $table->integer('min')->default(0);
            $table->integer('max')->default(0);
            $table->integer('weiblichmin')->default(0);
            $table->integer('weiblichmax')->default(0);
            $table->integer('manmin')->default(0);
            $table->integer('manmax')->default(0);
            $table->integer('training')->default(0);
            $table->integer('bahnen')->default(0);
            $table->boolean('zusatzmanschaft')->default(0);
            $table->text('beschreibung')->nullable();
            $table->string('distanz')->nullable();
            $table->decimal('meldeGebuehr',8, 2)->nullable();

            $table->unsignedBigInteger('autor_id');
            $table->unsignedBigInteger('bearbeiter_id');

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
        Schema::dropIfExists('race_type_template');
    }
};
