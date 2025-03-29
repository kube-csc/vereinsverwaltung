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
        Schema::table('coursedates', function (Blueprint $table) {
            $table->unsignedBigInteger('training_id')->nullable()->after('organiser_id');
            $table->integer('sportgeraeteGebucht')->default('0')->after('sportgeraetanzahl');
            $table->foreign('training_id')->references('id')->on('trainings');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('coursedates', function (Blueprint $table) {
            $table->dropForeign(['training_id']);
            $table->dropColumn('training_id');
            $table->dropColumn('sportgeraeteGebucht');
        });
    }
};
