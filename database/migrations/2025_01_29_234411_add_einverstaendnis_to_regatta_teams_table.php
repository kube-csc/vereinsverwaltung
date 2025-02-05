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
        Schema::table('regatta_teams', function (Blueprint $table) {
            $table->boolean('einverstaendnis')->default(false)->after('werbung');
            // DoTo: In der Laravalversion noch nicht möglich
            // $table->decimal('training' , 8 ,2)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('regatta_teams', function (Blueprint $table) {
            $table->dropColumn('einverstaendnis');
            // DoTo: In der Laravalversion noch nicht möglich
            // $table->boolean('training')->change();
        });
    }
};
