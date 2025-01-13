<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrganiserinformationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /**
         * Run the database seeds.
         */
        DB::table('organiserinformations')->delete();

        DB::table('organiserinformations')->insert(
            [
                array('id' => '1',
                    'organiser_id'                  => 1,
                    'veranstaltungBeschreibungLang' => 'Dies ist ein Beispiel Organisator für die Langen Beschreibung.',
                    'veranstaltungBeschreibungKurz' => 'Dies ist ein Beispiel Organisator für die Kurzen Beschreibung.',
                    'sportartBeschreibungLang'      => 'Dies ist ein Beispiel für die Sportart Beschreibung Lang.',
                    'sportartBeschreibungKurz'      => 'Dies ist ein Beispiel für die Sportart Beschreibung Kurz.',
                    'materialBeschreibungLang'      => 'Dies ist ein Beispiel für die Sportgeräte Beschreibung Lang.',
                    'materialBeschreibungKurz'      => 'Dies ist ein Beispiel für die Sportgeräte Beschreibung Kurz.',
                    'keineKurse'                    => 'Zur Zeit finden keine Kurse statt.',
                    'terminInformation'             => 'Dies ist ein Beispiel für die Termin Information.',
                    'mitgliedschaftKurz'            => 'Dies ist ein Beispiel für die Mitgliedschaft Kurz.',
                    'mitgliedschaftLang'            => 'Dies ist ein Beispiel für die Mitgliedschaft Lang.',
                    'autor_id'                      => 1,
                    'bearbeiter_id'                 => 1
                ),
                array('id' => '2',
                    'organiser_id'                  => 2,
                    'veranstaltungBeschreibungLang' => 'Dies ist ein Beispiel Organisator für die Langen Beschreibung.',
                    'veranstaltungBeschreibungKurz' => 'Dies ist ein Beispiel Organisator für die Kurzen Beschreibung.',
                    'veranstaltungBeschreibungLang' => 'Dies ist ein Beispiel Organisator für die Langen Beschreibung.',
                    'veranstaltungBeschreibungKurz' => 'Dies ist ein Beispiel Organisator für die Kurzen Beschreibung.',
                    'sportartBeschreibungLang'      => 'Dies ist ein Beispiel für die Sportart Beschreibung Lang.',
                    'sportartBeschreibungKurz'      => 'Dies ist ein Beispiel für die Sportart Beschreibung Kurz.',
                    'materialBeschreibungLang'      => 'Dies ist ein Beispiel für die Sportgeräte Beschreibung Lang.',
                    'materialBeschreibungKurz'      => 'Dies ist ein Beispiel für die Sportgeräte Beschreibung Kurz.',
                    'keineKurse'                    => 'Zur Zeit finden keine Kurse statt.',
                    'terminInformation'             => 'Dies ist ein Beispiel für die Termin Information.',
                    'mitgliedschaftKurz'            => 'Dies ist ein Beispiel für die Mitgliedschaft Kurz.',
                    'mitgliedschaftLang'            => 'Dies ist ein Beispiel für die Mitgliedschaft Lang.',
                    'autor_id'                      => 1,
                    'bearbeiter_id'                 => 1
                )
            ]);
    }
}
