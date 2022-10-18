<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClubSporttypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('club_sporttype', function (Blueprint $table) {
            $table->unsignedBigInteger('club_id')->nullable();
            $table->unsignedBigInteger('sporttype_id')->nullable();
            $table->timestamps();

            $table->primary(
                [
                    'club_id', 'sporttype_id'
                ]);

            $table->foreign('club_id')
                ->references('id')->on('clubs')
                ->onDelete('cascade');

            $table->foreign('sporttype_id')
                ->references('id')->on('sporttypes')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('club_sporttype');
    }
}
