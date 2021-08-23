<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_groups', function (Blueprint $table) {
            $table->id();
            $table->string('termingruppe', 50);
            $table->unsignedBigInteger('user_id')->nullable();
            $table->SoftDeletes();
            $table->timestamps();

            $table->foreign('user_id')
              ->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_groups');
    }
}
