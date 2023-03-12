<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OperationalLocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('operational_locations')->delete();

        \DB::table('operational_locations')->insert(
            array(
                array('id' => '1', 'einsatzort' => 'Cafeteria', 'beschreibung' => NULL, 'autor_id' => '1', 'bearbeiter_id' => '1', 'freigeber_id' => NULL, 'letzteFreigabe' => NULL, 'deleted_at' => NULL, 'created_at' => '2023-02-18 15:57:19', 'updated_at' => '2023-02-18 15:57:19'),
                array('id' => '2', 'einsatzort' => 'Grill', 'beschreibung' => NULL, 'autor_id' => '1', 'bearbeiter_id' => '1', 'freigeber_id' => NULL, 'letzteFreigabe' => NULL, 'deleted_at' => NULL, 'created_at' => '2023-02-18 15:57:41', 'updated_at' => '2023-02-18 15:57:41'),
                array('id' => '3', 'einsatzort' => 'Pommes', 'beschreibung' => NULL, 'autor_id' => '1', 'bearbeiter_id' => '1', 'freigeber_id' => NULL, 'letzteFreigabe' => NULL, 'deleted_at' => NULL, 'created_at' => '2023-02-18 15:57:48', 'updated_at' => '2023-02-18 15:57:48'),
                array('id' => '4', 'einsatzort' => 'Getränke / Waffeln', 'beschreibung' => NULL, 'autor_id' => '1', 'bearbeiter_id' => '1', 'freigeber_id' => NULL, 'letzteFreigabe' => NULL, 'deleted_at' => NULL, 'created_at' => '2023-02-18 15:58:07', 'updated_at' => '2023-02-18 15:58:07'),
                array('id' => '5', 'einsatzort' => 'Wertmarkenverkauf', 'beschreibung' => NULL, 'autor_id' => '1', 'bearbeiter_id' => '1', 'freigeber_id' => NULL, 'letzteFreigabe' => NULL, 'deleted_at' => NULL, 'created_at' => '2023-02-18 15:58:16', 'updated_at' => '2023-02-18 15:58:29'),
                array('id' => '6', 'einsatzort' => 'Parkeinweiser', 'beschreibung' => NULL, 'autor_id' => '1', 'bearbeiter_id' => '1', 'freigeber_id' => NULL, 'letzteFreigabe' => NULL, 'deleted_at' => NULL, 'created_at' => '2023-02-18 15:58:47', 'updated_at' => '2023-02-18 16:14:34'),
                array('id' => '7', 'einsatzort' => 'Küchenhilfe', 'beschreibung' => NULL, 'autor_id' => '1', 'bearbeiter_id' => '1', 'freigeber_id' => NULL, 'letzteFreigabe' => NULL, 'deleted_at' => NULL, 'created_at' => '2023-02-18 15:58:47', 'updated_at' => '2023-02-18 16:14:34')
            )
        );
    }
}
