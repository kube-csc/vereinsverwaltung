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
                $table->string('liveDomain')->after('domain')->nullable();
                $table->string('headerTitel')->after('liveDomain')->nullable();
                $table->string('headerSlogen')->after('headerTitel')->nullable();
                $table->string('headerBild')->after('headerSlogen')->nullable();
                $table->string('accentColor', 9)->nullable()->after('headerBild');
                $table->unsignedBigInteger('bearbeiter_id')->nullable()->after('user_id');
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
            $table->dropColumn('liveDomain');
            $table->dropColumn('headerBild');
            $table->dropColumn('headerSlogen');
            $table->dropColumn('headerTitel');
            $table->dropColumn('accentColor');
            $table->dropColumn('bearbeiter_id');
        });
    }
};
