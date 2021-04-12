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
    //  \App\Models\Instruction::factory(1)->create();
      DB::table('instructions')->delete();

      DB::table('instructions')
        ->insert(
          [
            'ueberschrift'  => 'Datenschutzerklärung',
            'beschreibung'  => 'Bitte geben Sie hier ihre Datenschutzerklärung ein.',
            'created_at'    => '2021-03-28 13:06:42',
            'updated_at'    => '2021-03-28 13:06:42',
           ]
        );

    }
}
