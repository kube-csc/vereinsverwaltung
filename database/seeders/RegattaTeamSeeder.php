<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RegattaTeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('regatta_teams')->delete();

        $currentDate1 = Carbon::now()->subDay(4)->toDateString();
        $currentDate2 = Carbon::now()->subDay(3)->toDateString();
        $currentDate3 = Carbon::now()->subDay(2)->toDateString();

        $data = [
            array('id' => '1','datum' => $currentDate1,'teamname' => 'Regatta Team 1','verein' => '','teamcaptain' => '','strasse' => '','plz' => '','ort' => '','telefon' => '','email' => '','homepage' => '','status' => 'Neuanmeldung','training' => '1','regatta_id' => '8','gruppe_id' => '1','passwort' => '5','bild' => 'teamImage-1.jpg','pixx' => '0','pixy' => '0','beschreibung' => '','teamlink' => '1','kommentar' => '','mannschaftsmail' => '','mailen' => 'n','mailendatum' => null,'werbung' => '0'),
            array('id' => '2','datum' => $currentDate1,'teamname' => 'Regatta Team 2','verein' => '','teamcaptain' => '','strasse' => '','plz' => '','ort' => '','telefon' => '','email' => '','homepage' => '','status' => 'Neuanmeldung','training' => '1','regatta_id' => '8','gruppe_id' => '1','passwort' => '5','bild' => '','pixx' => '0','pixy' => '0','beschreibung' => '','teamlink' => '1','kommentar' => '','mannschaftsmail' => '','mailen' => 'n','mailendatum' => null,'werbung' => '0'),
            array('id' => '3','datum' => $currentDate1,'teamname' => 'Regatta Team 3','verein' => '','teamcaptain' => '','strasse' => '','plz' => '','ort' => '','telefon' => '','email' => '','homepage' => '','status' => 'Neuanmeldung','training' => '1','regatta_id' => '8','gruppe_id' => '1','passwort' => '5','bild' => '','pixx' => '0','pixy' => '0','beschreibung' => '','teamlink' => '1','kommentar' => '','mannschaftsmail' => '','mailen' => 'n','mailendatum' => null,'werbung' => '0'),
            array('id' => '4','datum' => $currentDate1,'teamname' => 'Regatta Team 4','verein' => '','teamcaptain' => '','strasse' => '','plz' => '','ort' => '','telefon' => '','email' => '','homepage' => '','status' => 'Neuanmeldung','training' => '1','regatta_id' => '8','gruppe_id' => '1','passwort' => '5','bild' => '','pixx' => '0','pixy' => '0','beschreibung' => '','teamlink' => '1','kommentar' => '','mannschaftsmail' => '','mailen' => 'n','mailendatum' => null,'werbung' => '0'),
            array('id' => '5','datum' => $currentDate2,'teamname' => 'Regatta Team 5','verein' => '','teamcaptain' => '','strasse' => '','plz' => '','ort' => '','telefon' => '','email' => '','homepage' => '','status' => 'Neuanmeldung','training' => '1','regatta_id' => '8','gruppe_id' => '2','passwort' => '5','bild' => '','pixx' => '0','pixy' => '0','beschreibung' => '','teamlink' => '1','kommentar' => '','mannschaftsmail' => '','mailen' => 'n','mailendatum' => null,'werbung' => '0'),
            array('id' => '6','datum' => $currentDate2,'teamname' => 'Regatta Team 6','verein' => '','teamcaptain' => '','strasse' => '','plz' => '','ort' => '','telefon' => '','email' => '','homepage' => '','status' => 'Neuanmeldung','training' => '1','regatta_id' => '8','gruppe_id' => '2','passwort' => '5','bild' => '','pixx' => '0','pixy' => '0','beschreibung' => '','teamlink' => '1','kommentar' => '','mannschaftsmail' => '','mailen' => 'n','mailendatum' => null,'werbung' => '0'),
            array('id' => '7','datum' => $currentDate2,'teamname' => 'Regatta Team 7','verein' => '','teamcaptain' => '','strasse' => '','plz' => '','ort' => '','telefon' => '','email' => '','homepage' => '','status' => 'Neuanmeldung','training' => '1','regatta_id' => '8','gruppe_id' => '2','passwort' => '5','bild' => '','pixx' => '0','pixy' => '0','beschreibung' => '','teamlink' => '1','kommentar' => '','mannschaftsmail' => '','mailen' => 'n','mailendatum' => null,'werbung' => '0'),
            array('id' => '8','datum' => $currentDate3,'teamname' => 'Regatta Team 8','verein' => '','teamcaptain' => '','strasse' => '','plz' => '','ort' => '','telefon' => '','email' => '','homepage' => '','status' => 'Neuanmeldung','training' => '1','regatta_id' => '8','gruppe_id' => '2','passwort' => '5','bild' => '','pixx' => '0','pixy' => '0','beschreibung' => '','teamlink' => '1','kommentar' => '','mannschaftsmail' => '','mailen' => 'n','mailendatum' => null,'werbung' => '0'),
        ];

        DB::table('regatta_teams')->insert($data);
    }
}
