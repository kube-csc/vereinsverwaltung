<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('instruction_id')->nullable();
            $table->unsignedBigInteger('sportSection_id')->nullable();
            $table->string('dokumentenName','40');
            $table->string('dokumentenFile')->nullable();
            $table->date('startDatum')->nullable();
            $table->date('endDatum')->nullable();
            $table->boolean('footerStatus')->default(true);  // true = 1 = sichtbar
            $table->boolean('visible')->default(true);       // true = 1 = sichtbar
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
        Schema::dropIfExists('documents');
    }
}
