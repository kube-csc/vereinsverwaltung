<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class InstructionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      \App\Models\Instruction::factory(1)->create();
    }
}
