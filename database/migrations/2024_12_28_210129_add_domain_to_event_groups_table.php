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
        Schema::table('event_groups', function (Blueprint $table) {
                $table->string('domain')->after('visible')->nullable();
                $table->string('headerTitel')->after('domain')->nullable();
                $table->string('headerSlogen')->after('headerTitel')->nullable();
                $table->string('headerBild')->after('headerSlogen')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('event_groups', function (Blueprint $table) {
            $table->dropColumn('domain');
            $table->dropColumn('headerBild');
        });
    }
};
