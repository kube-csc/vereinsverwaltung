<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVeranstaltungHeaderKleinToOrganisersTable extends Migration
{
    public function up(): void
    {
        Schema::table('organisers', function (Blueprint $table) {
            // neue optionale Spalte für kleinen Veranstaltungs-Header
            $table->string('veranstaltungHeaderKlein')->nullable()->after(veranstaltungHeader);
        });
    }

    public function down(): void
    {
        Schema::table('organisers', function (Blueprint $table) {
            $table->dropColumn('veranstaltungHeaderKlein');
        });
    }
}
