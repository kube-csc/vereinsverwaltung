<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InstructionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('instructions')->delete();

      DB::table('instructions')
          ->insert(
              [
                  array(
                      'id' => '1',
                      'ueberschrift'    => 'Datenschutzerklärung',
                      'beschreibung'    => 'Bitte geben Sie hier ihre Datenschutzerklärung ein.',
                      'hauptmenu'       => '0',
                      'hauptmenuspalte' => '0',
                      'systemmenu'      => '1',
                      'position'        => '10',
                      'route'           => 'x',
                      'visible'         => '1',
                      'user_id'         => '1',
                      'bearbeiter_id'   => '1',
                      'created_at'      => '2021-03-28 13:06:42',
                      'updated_at'      => '2021-03-28 13:06:42',
                  ),
                  array(
                      'id' => '2',
                      'ueberschrift'    => 'Impressum',
                      'beschreibung'    => 'Bitte geben Sie hier ihre Informationen zum Impressum ein.',
                      'hauptmenu'       => '0',
                      'hauptmenuspalte' => '0',
                      'systemmenu'      => '1',
                      'position'        => '20',
                      'route'           => 'x',
                      'visible'         => '1',
                      'user_id'         => '1',
                      'bearbeiter_id'   => '1',
                      'created_at'      => '2021-03-28 13:06:42',
                      'updated_at'      => '2021-03-28 13:06:42',
                  ),
                  array(
                      'id' => '3',
                      'ueberschrift'    => 'Sport',
                      'beschreibung'    => '',
                      'hauptmenu'       => '2',
                      'hauptmenuspalte' => '10',
                      'systemmenu'      => '0',
                      'position'        => '0',
                      'route'           => Null,
                      'visible'         => '1',
                      'user_id'         => '1',
                      'bearbeiter_id'   => '1',
                      'created_at'      => '2021-03-28 13:06:42',
                      'updated_at'      => '2021-03-28 13:06:42',
                  ),
                  array(
                      'id' => '4',
                      'ueberschrift'    => 'Vereine',
                      'beschreibung'    => '',
                      'hauptmenu'       => '3',
                      'hauptmenuspalte' => '10',
                      'systemmenu'      => '1',
                      'position'        => '10',
                      'route'           => 'MENUE_VEREIN',
                      'visible'         => '1',
                      'user_id'         => '1',
                      'bearbeiter_id'   => '1',
                      'created_at'      => '2021-03-28 13:06:42',
                      'updated_at'      => '2021-03-28 13:06:42',
                  ),
                  array(
                      'id' => '5',
                      'ueberschrift'    => 'Sportarten',
                      'beschreibung'    => '',
                      'hauptmenu'       => '3',
                      'hauptmenuspalte' => '10',
                      'systemmenu'      => '1',
                      'position'        => '20',
                      'route'           => 'MENUE_VERBAND',
                      'visible'         => '1',
                      'user_id'         => '1',
                      'bearbeiter_id'   => '1',
                      'created_at'      => '2021-03-28 13:06:42',
                      'updated_at'      => '2021-03-28 13:06:42',
                  ),
                  array(
                      'id' => '6',
                      'ueberschrift'    => 'Information',
                      'beschreibung'    => '',
                      'hauptmenu'       => '2',
                      'hauptmenuspalte' => '20',
                      'systemmenu'      => '0',
                      'position'        => '0',
                      'route'           => Null,
                      'visible'         => '1',
                      'user_id'         => '1',
                      'bearbeiter_id'   => '1',
                      'created_at'      => '2021-03-28 13:06:42',
                      'updated_at'      => '2021-03-28 13:06:42',
                  ),
                  array(
                      'id' => '7',
                      'ueberschrift'    => 'Anfahrt',
                      'beschreibung'    => '',
                      'hauptmenu'       => '3',
                      'hauptmenuspalte' => '20',
                      'systemmenu'      => '1',
                      'position'        => '10',
                      'route'           => '/Anfahrt',
                      'visible'         => '1',
                      'user_id'         => '1',
                      'bearbeiter_id'   => '1',
                      'created_at'      => '2021-03-28 13:06:42',
                      'updated_at'      => '2021-03-28 13:06:42',
                  ),
                  array(
                      'id' => '8',
                      'ueberschrift'    => 'Beiträge',
                      'beschreibung'    => 'Bitte geben Sie hier ihre Beitragssetze ein.',
                      'hauptmenu'       => '3',
                      'hauptmenuspalte' => '20',
                      'systemmenu'      => '0',
                      'position'        => '20',
                      'route'           => Null,
                      'visible'         => '1',
                      'user_id'         => '1',
                      'bearbeiter_id'   => '1',
                      'created_at'      => '2021-03-28 13:06:42',
                      'updated_at'      => '2021-03-28 13:06:42',
                  ),
                  array(
                      'id' => '9',
                      'ueberschrift'    => 'Übernachtungskosten',
                      'beschreibung'    => 'Bitte geben Sie hier ihre Übernachtungskosten ein.',
                      'hauptmenu'       => '3',
                      'hauptmenuspalte' => '20',
                      'systemmenu'      => '0',
                      'position'        => '30',
                      'route'           => Null,
                      'visible'         => '1',
                      'user_id'         => '1',
                      'bearbeiter_id'   => '1',
                      'created_at'      => '2021-03-28 13:06:42',
                      'updated_at'      => '2021-03-28 13:06:42',
                  ),
              ]);
    }
}

