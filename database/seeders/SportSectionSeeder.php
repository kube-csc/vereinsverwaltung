<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;

class SportSectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       DB::table('sport_sections')
        ->insert(
          [
            array('ID' => '1','abteilung' => 'Jugend','idtermin' => '0','status' => '2','idabteilung' => '0','bild' => '','domain' => '','iduser' => '20','created_at' => '2021-03-28 13:06:42','updated_at' => '2021-03-28 13:06:42'),
            array('ID' => '2','abteilung' => 'Rennsport','idtermin' => '0','status' => '2','idabteilung' => '0','bild' => '','domain' => '','iduser' => '20','created_at' => '2021-03-28 13:06:42','updated_at' => '2021-03-28 13:06:42'),
            array('ID' => '3','abteilung' => 'Wandersport','idtermin' => '1023','status' => '2','idabteilung' => '0','bild' => '','domain' => '','iduser' => '20','created_at' => '2021-03-28 13:06:42','updated_at' => '2021-03-28 13:06:42'),
            array('ID' => '4','abteilung' => 'Emscher Lippe Dragons','idtermin' => '0','status' => '2','idabteilung' => '11','bild' => '','domain' => '','iduser' => '20','created_at' => '2021-03-28 13:06:42','updated_at' => '2021-03-28 13:06:42'),
            array('ID' => '5','abteilung' => 'Frauen','idtermin' => '0','status' => '0','idabteilung' => '0','bild' => '','domain' => '','iduser' => '20','created_at' => '2021-03-28 13:06:42','updated_at' => '2021-03-28 13:06:42'),
            array('ID' => '6','abteilung' => 'Senioren','idtermin' => '0','status' => '2','idabteilung' => '0','bild' => '','domain' => '','iduser' => '20','created_at' => '2021-03-28 13:06:42','updated_at' => '2021-03-28 13:06:42'),
            array('ID' => '7','abteilung' => 'Verein','idtermin' => '1019','status' => '1','idabteilung' => '0','bild' => '','domain' => '','iduser' => '20','created_at' => '2021-03-28 13:06:42','updated_at' => '2021-03-28 13:06:42'),
            array('ID' => '8','abteilung' => 'Stand Up','idtermin' => '1021','status' => '2','idabteilung' => '0','bild' => '','domain' => '','iduser' => '20','created_at' => '2021-03-28 13:06:42','updated_at' => '2021-03-28 13:06:42'),
            array('ID' => '9','abteilung' => 'Pink Dragons','idtermin' => '1020','status' => '2','idabteilung' => '11','bild' => '','domain' => '','iduser' => '20','created_at' => '2021-03-28 13:06:42','updated_at' => '2021-03-28 13:06:42'),
            array('ID' => '10','abteilung' => 'Outigger','idtermin' => '0','status' => '2','idabteilung' => '0','bild' => '','domain' => '','iduser' => '20','created_at' => '2021-03-28 13:06:42','updated_at' => '2021-03-28 13:06:42'),
            array('ID' => '11','abteilung' => 'Drachenboot','idtermin' => '1190','status' => '2','idabteilung' => '0','bild' => '','domain' => '','iduser' => '20','created_at' => '2021-03-28 13:06:42','updated_at' => '2021-03-28 13:06:42'),
            array('ID' => '12','abteilung' => 'Ohana','idtermin' => '0','status' => '2','idabteilung' => '11','bild' => '','domain' => '','iduser' => '20','created_at' => '2021-03-28 13:06:42','updated_at' => '2021-03-28 13:06:42'),
          ]
        );
     }
}

/*
// kel-orginal`.`verein1_abteilung`
$verein1_abteilung = array(
  array('ID' => '1','abteilung' => 'Jugend','idtermin' => '0','status' => '2','idabteilung' => '0','bild' => '','domain' => ''),
  array('ID' => '2','abteilung' => 'Rennsport','idtermin' => '0','status' => '2','idabteilung' => '0','bild' => 'hero-bg-rennsport.jpg','domain' => ''),
  array('ID' => '3','abteilung' => 'Wandersport','idtermin' => '1023','status' => '2','idabteilung' => '0','bild' => 'hero-bg-wander.jpg','domain' => ''),
  array('ID' => '4','abteilung' => 'Emscher Lippe Dragons','idtermin' => '0','status' => '2','idabteilung' => '11','bild' => 'hero-bg-drachenboot.jpg','domain' => ''),
  array('ID' => '5','abteilung' => 'Frauen','idtermin' => '0','status' => '0','idabteilung' => '0','bild' => '','domain' => ''),
  array('ID' => '6','abteilung' => 'Senioren','idtermin' => '0','status' => '2','idabteilung' => '0','bild' => '','domain' => ''),
  array('ID' => '7','abteilung' => 'Verein','idtermin' => '1019','status' => '1','idabteilung' => '0','bild' => 'hero-bg.jpg','domain' => ''),
  array('ID' => '8','abteilung' => 'Stand Up','idtermin' => '1021','status' => '2','idabteilung' => '0','bild' => 'hero-bg-sup.jpg','domain' => ''),
  array('ID' => '9','abteilung' => 'Pink Dragons','idtermin' => '1020','status' => '2','idabteilung' => '11','bild' => 'hero-bg-pink.jpg','domain' => ''),
  array('ID' => '10','abteilung' => 'Outigger','idtermin' => '0','status' => '2','idabteilung' => '0','bild' => '','domain' => ''),
  array('ID' => '11','abteilung' => 'Drachenboot','idtermin' => '1190','status' => '2','idabteilung' => '0','bild' => 'hero-bg-drachenboot.jpg','domain' => ''),
  array('ID' => '12','abteilung' => 'Ohana','idtermin' => '0','status' => '0','idabteilung' => '11','bild' => 'hero-bg-ohana.jpg','domain' => '')
);
*/
