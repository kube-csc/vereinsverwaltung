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
                      'ueberschrift'  => 'Datenschutzerklärung',
                      'beschreibung'  => 'Bitte geben Sie hier ihre Datenschutzerklärung ein.',
                      'visible'       =>  '1',
                      'created_at'    => '2021-03-28 13:06:42',
                      'updated_at'    => '2021-03-28 13:06:42',
                  ),
                  array(
                      'id' => '2',
                      'ueberschrift'  => 'Beiträge',
                      'beschreibung'  => 'Bitte geben Sie hier ihre Beitragsetze ein.',
                      'visible'       =>  '1',
                      'created_at'    => '2021-03-28 13:06:42',
                      'updated_at'    => '2021-03-28 13:06:42',
                  ),
                  array(
                      'id' => '3',
                      'ueberschrift'  => 'Übernachtungskosten',
                      'beschreibung'  => 'Bitte geben Sie hier ihre Übernachtungskosten ein.',
                      'visible'       =>  '1',
                      'created_at'    => '2021-03-28 13:06:42',
                      'updated_at'    => '2021-03-28 13:06:42',
                  ),
              ]);
    }
}

