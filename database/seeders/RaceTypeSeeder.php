<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RaceTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'id' => 1,
                'regatta_id' => 8,
                'race_type_template_id' => 1,
                'typ' => 'Klasse 1',
                'altervon' => 0,
                'alterbis' => 0,
                'min' => 0,
                'max' => 0,
                'weiblichmin' => 0,
                'weiblichmax' => 0,
                'manmin' => 0,
                'manmax' => 0,
                'training' => 1,
                'max_trainingstermine' => 3,
                'training_preis' => 0,
                'bahnen' => 0,
                'zusatzmanschaft' => 0,
                'beschreibung' => null,
                'distanz' => '250 m',
                'meldeGebuehr' => 100,
                'autor_id' => 1,
                'bearbeiter_id' => 1,
                'created_at' => '2024-08-18 13:06:42',
                'updated_at' => '2024-08-18 13:06:42',
            ],
            [
                'id' => 2,
                'regatta_id' => 8,
                'race_type_template_id' => 1,
                'typ' => 'Klasse 2',
                'altervon' => 0,
                'alterbis' => 0,
                'min' => 0,
                'max' => 0,
                'weiblichmin' => 0,
                'weiblichmax' => 0,
                'manmin' => 0,
                'manmax' => 0,
                'training' => 1,
                // maximal 3 Trainingszeiten/Trainingstermine buchbar
                'max_trainingstermine' => 3,
                'training_preis' => 0,
                'bahnen' => 0,
                'zusatzmanschaft' => 0,
                'beschreibung' => null,
                'distanz' => '250 m',
                'meldeGebuehr' => 100,
                'autor_id' => 1,
                'bearbeiter_id' => 1,
                'created_at' => '2024-08-18 13:06:42',
                'updated_at' => '2024-08-18 13:06:42',
            ],
        ];

        // Kein DELETE, da race_types per FK referenziert werden kann.
        // Upsert aktualisiert bestehende IDs oder legt sie neu an.
        DB::table('race_types')->upsert(
            $data,
            ['id'],
            [
                'regatta_id',
                'race_type_template_id',
                'typ',
                'altervon',
                'alterbis',
                'min',
                'max',
                'weiblichmin',
                'weiblichmax',
                'manmin',
                'manmax',
                'training',
                'max_trainingstermine',
                'training_preis',
                'bahnen',
                'zusatzmanschaft',
                'beschreibung',
                'distanz',
                'meldeGebuehr',
                'autor_id',
                'bearbeiter_id',
                'updated_at',
            ]
        );
    }
}
