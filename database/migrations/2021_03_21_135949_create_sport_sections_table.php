<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSportSectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sport_sections', function (Blueprint $table) {
            $table->id();
            $table->string('abteilung');
            $table->unsignedBigInteger('idtermin')->nullable();
            $table->integer('status')->nullable();
            $table->unsignedBigInteger('idabteilung')->nullable();
            $table->string('bild')->nullable();
            $table->string('domain')->nullable();
            $table->unsignedBigInteger('iduser');
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
        Schema::dropIfExists('sport_sections');
    }
}



/*
$table->unsignedBigInteger('user_id')->nullable()->after('id');
$table->foreign('user_id')
      ->references('id')->on('users')
      ->onDelete('cascade');


CREATE TABLE `verein1_abteilung` (
  `ID` int(11) NOT NULL,
  `abteilung` varchar(40) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `idtermin` int(5) NOT NULL,
  `status` int(1) NOT NULL,
  `idabteilung` int(3) NOT NULL,
  `bild` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `domain` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
*/
