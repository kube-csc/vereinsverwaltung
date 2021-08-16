<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

                    \DB::table('events')->delete();


                            \DB::table('events')->insert(array (
                                0 =>
                                array (
                                    'id' => 1,
                                    'datumvon' => '2005-05-21',
                                    'datumbis' => '2005-05-22',
                                    'datumvona' => NULL,
                                    'datumbisa' => NULL,
                                    'veranstaltung' => '',
                                    'ueberschrift' => 'Unser Verein',
                                    'beschreibung' => 'Hier steht die Vereinsbeschreibung',
                                    'Ansprechparten' => '',
                                    'telefon' => '',
                                    'email' => '',
                                    'homepage' => '',
                                    'sportSection_id' => '1',
                                    'startseite' => '',
                                    'nachtermin' => '',
                                    'anmeldetext' => '',
                                    'onlinemeldung' => '0',
                                    'created_at' => '2021-03-28 13:06:42',
                                    'regatta' => 0,
                                    'verwendung' => 4,
                                    'idterminvor' => 0,
                                    'einverstaendnis' => '',
                                    'teilnehmer' => 0,
                                    'teilnehmermax' => 0,
                                    'autor_id' => 1,
                                    'bearbeiter_id' => 1,
                                    'updated_at' => '2021-03-28 13:06:42',
                                    'freigabe' => '',
                                ),
                                1 =>
                                array (
                                    'id' => 2,
                                    'datumvon' => '2005-06-11',
                                    'datumbis' => '2005-06-11',
                                    'datumvona' => NULL,
                                    'datumbisa' => NULL,
                                    'veranstaltung' => '',
                                    'ueberschrift' => 'Abteilung 1',
                                    'beschreibung' => 'Hier steht die Beschreibung von der Abteilung 1',
                                    'Ansprechparten' => '',
                                    'telefon' => '',
                                    'email' => '',
                                    'homepage' => '',
                                    'sportSection_id' => '2',
                                    'startseite' => '',
                                    'nachtermin' => '',
                                    'anmeldetext' => '',
                                    'onlinemeldung' => '0',
                                    'created_at' => '2021-03-28 13:06:42',
                                    'regatta' => 0,
                                    'verwendung' => 4,
                                    'idterminvor' => 0,
                                    'einverstaendnis' => '',
                                    'teilnehmer' => 0,
                                    'teilnehmermax' => 0,
                                    'autor_id' => 1,
                                    'bearbeiter_id' => 1,
                                    'updated_at' => '2021-03-28 13:06:42',
                                    'freigabe' => '',
                                ),
                                2 =>
                                array (
                                    'id' => 3,
                                    'datumvon' => '2005-06-11',
                                    'datumbis' => '2005-06-11',
                                    'datumvona' => NULL,
                                    'datumbisa' => NULL,
                                    'veranstaltung' => '',
                                    'ueberschrift' => 'Abteilung 2',
                                    'beschreibung' => 'Hier steht die Beschreibung von der Abteilung 2',
                                    'Ansprechparten' => '',
                                    'telefon' => '',
                                    'email' => '',
                                    'homepage' => '',
                                    'sportSection_id' => '3',
                                    'startseite' => '',
                                    'nachtermin' => '',
                                    'anmeldetext' => '',
                                    'onlinemeldung' => '0',
                                    'created_at' => '2021-03-28 13:06:42',
                                    'regatta' => 0,
                                    'verwendung' => 4,
                                    'idterminvor' => 0,
                                    'einverstaendnis' => '',
                                    'teilnehmer' => 0,
                                    'teilnehmermax' => 0,
                                    'autor_id' => 1,
                                    'bearbeiter_id' => 1,
                                    'updated_at' => '2021-03-28 13:06:42',
                                    'freigabe' => '',
                                ),
                                3 =>
                                array (
                                    'id' => 4,
                                    'datumvon' => '2005-06-11',
                                    'datumbis' => '2005-06-11',
                                    'datumvona' => NULL,
                                    'datumbisa' => NULL,
                                    'veranstaltung' => '',
                                    'ueberschrift' => 'Mannschaft 1',
                                    'beschreibung' => 'Hier steht die Beschreibung von der Mannschaft 1',
                                    'Ansprechparten' => '',
                                    'telefon' => '',
                                    'email' => '',
                                    'homepage' => '',
                                    'sportSection_id' => '4',
                                    'startseite' => '',
                                    'nachtermin' => '',
                                    'anmeldetext' => '',
                                    'onlinemeldung' => '0',
                                    'created_at' => '2021-03-28 13:06:42',
                                    'regatta' => 0,
                                    'verwendung' => 4,
                                    'idterminvor' => 0,
                                    'einverstaendnis' => '',
                                    'teilnehmer' => 0,
                                    'teilnehmermax' => 0,
                                    'autor_id' => 1,
                                    'bearbeiter_id' => 1,
                                    'updated_at' => '2021-03-28 13:06:42',
                                    'freigabe' => '',
                                ),
                                4 =>
                                array (
                                    'id' => 5,
                                    'datumvon' => '2005-06-11',
                                    'datumbis' => '2005-06-11',
                                    'datumvona' => NULL,
                                    'datumbisa' => NULL,
                                    'veranstaltung' => '',
                                    'ueberschrift' => 'Mannschaft 2',
                                    'beschreibung' => 'Hier steht die Beschreibung von der Mannschaft 2',
                                    'Ansprechparten' => '',
                                    'telefon' => '',
                                    'email' => '',
                                    'homepage' => '',
                                    'sportSection_id' => '5',
                                    'startseite' => '',
                                    'nachtermin' => '',
                                    'anmeldetext' => '',
                                    'onlinemeldung' => '0',
                                    'created_at' => '2021-03-28 13:06:42',
                                    'regatta' => 0,
                                    'verwendung' => 4,
                                    'idterminvor' => 0,
                                    'einverstaendnis' => '',
                                    'teilnehmer' => 0,
                                    'teilnehmermax' => 0,
                                    'autor_id' => 1,
                                    'bearbeiter_id' => 1,
                                    'updated_at' => '2021-03-28 13:06:42',
                                    'freigabe' => '',
                                ),

                    ));


    }
}
