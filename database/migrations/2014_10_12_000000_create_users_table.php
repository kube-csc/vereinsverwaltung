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
          $table->string('nachname', 40);
          $table->string('vorname', 40);
          $table->string('geschlecht', 1)->nullable();
          $table->date('geburtsdatum')->nullable();
          $table->date('vereinseintritt')->nullable();
          $table->date('vereinsaustritt')->nullable();
          $table->foreignId('sportSections_id')->default(0);
          $table->string('password_alt', 20)->nullable();
          $table->integer('webspace')->default(0);
          $table->integer('regattatrainer')->default(0);
          $table->char('trainernachricht', 1)->default('');
          $table->decimal('gewicht', 4, 1)->default(0.0);
          $table->integer('position')->default(0);
          $table->integer('seite')->default(0);
          $table->integer('status')->default(0);
          $table->integer('admin')->default(0);
          $table->foreignId('vorstand_id')->default(0);
          $table->text('beschreibung')->nullable();
          $table->string('telefon', 25)->default('');
          $table->string('bild', 15)->default('');
          $table->integer('pixx')->default(0);
          $table->integer('pixy')->default(0);

          //Laravel 8
          $table->string('name')->nullable();
          $table->string('email')->unique();
          $table->timestamp('email_verified_at')->nullable();
          $table->string('password');
          $table->rememberToken();
          $table->foreignId('current_team_id')->nullable();
          $table->text('profile_photo_path')->nullable();
          $table->SoftDeletes();
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
