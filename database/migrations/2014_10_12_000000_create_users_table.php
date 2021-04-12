<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
          $table->id();
          //Vereinsverwaltung
          $table->string('nachname', 40)->default('');
          $table->string('vorname', 40)->default('');
          //Laravel
          $table->string('name');
          $table->string('email')->unique();
          $table->timestamp('email_verified_at')->nullable();
          $table->string('password');
          $table->rememberToken();
          $table->foreignId('current_team_id')->nullable();
          $table->text('profile_photo_path')->nullable();
          $table->timestamps();
        }
      );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}



/*
$table->id();
//Vereinsverwaltung
$table->string('nachname', 40)->default('');
$table->string('vorname', 40)->default('');
$table->string('geschlecht', 1);
$table->date('geburtsdatum')->nullable();
$table->date('vereinseintritt')->nullable();
$table->date('vereinsaustritt')->nullable();
$table->integer('idgruppe')->default(0);
$table->string('password_alt', 20)->default('');
//laravel
$table->string('password');
//Vereinsverwaltung
$table->integer('admin')->default(0);
$table->integer('idvorstand')->default(0);
$table->text('beschreibung');
//Laravel
$table->string('email')->unique();
//$table->string('email')->unique();
//Vereinsverwaltung
$table->string('telefon', 25)->default('');
$table->string('bild', 15)->default('');
$table->integer('pixx')->default(0);
$table->integer('pixy')->default(0);
//Laravel
$table->string('name');
//Vereinsverwaltung
$table->integer('webspace')->default(0);
$table->integer('regattatrainer')->default(0);
$table->char('trainernachricht', 1)->default('');
$table->decimal('gewicht', 4, 1)->default(0.0);
$table->integer('position')->default(0);
$table->integer('seite')->default(0);
$table->integer('status')->default(0);
//Laravel
$table->timestamp('email_verified_at')->nullable();
$table->rememberToken();
$table->foreignId('current_team_id')->nullable();
$table->text('profile_photo_path')->nullable();
$table->timestamps();

/*
Vereinsverwaltung
$table->integer('idmitglied', true);
$table->string('nachname', 40)->default('');
$table->string('vorname', 40)->default('');
$table->string('geschlecht', 1);
$table->date('geburtsdatum');
$table->date('vereinseintritt');
$table->date('vereinsaustritt');
$table->integer('idgruppe')->default(0);
$table->string('password', 20)->default('');
$table->string('passwort');
$table->integer('admin')->default(0);
$table->integer('idvorstand')->default(0);
$table->binary('beschreibung', 65535);
$table->string('email', 60);
$table->string('telefon', 25)->default('');
$table->string('bild', 15)->default('');
$table->integer('pixx')->default(0);
$table->integer('pixy')->default(0);
$table->string('loginname', 40)->default('');
$table->integer('webspace')->default(0);
$table->integer('regattatrainer')->default(0);
$table->char('trainernachricht', 1)->default('');
$table->decimal('gewicht', 4, 1)->default(0.0);
$table->integer('position')->default(0);
$table->integer('seite')->default(0);
$table->integer('status')->default(0);
*/

/* Orginal
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->timestamp('email_verified_at')->nullable();
    $table->string('password');
    $table->rememberToken();
    $table->foreignId('current_team_id')->nullable();
    $table->text('profile_photo_path')->nullable();
    $table->timestamps();
*/
