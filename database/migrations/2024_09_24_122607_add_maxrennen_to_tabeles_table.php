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
        Schema::table('tabeles', function (Blueprint $table) {
            $table->integer('maxrennen')->default(0)->after('status');
            $table->boolean('buchholzwertungaktiv')->default('0')->after('wertungsart');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tabeles', function (Blueprint $table) {
            $table->dropColumn('maxrennen');
            $table->dropColumn('buchholzzahlaktiv');
        });
    }
};
