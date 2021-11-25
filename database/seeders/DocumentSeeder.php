<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('documents')->delete();

        DB::table('documents')
            ->insert(
                [
                array(
                    'id' => '1',
                    'instruction_id' => NULL,
                    'sportSection_id' => NULL,
                    'dokumentenName' => 'Dokument zum Downloaden',
                    'dokumentenFile' => 'DemoDokument.pdf',
                    'startDatum' => '2021-11-25',
                    'endDatum' => '2099-12-31',
                    'footerStatus' => '1',
                    'visible' => '1',
                    'bearbeiter_id' => '1',
                    'user_id' => '1',
                    'deleted_at' => NULL,
                    'created_at' => '2021-11-23 00:10:16',
                    'updated_at' => '2021-11-25 00:31:25'
                )
            ]);
    }
}
