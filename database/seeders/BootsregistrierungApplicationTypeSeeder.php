<?php

namespace Database\Seeders;

class BootsregistrierungApplicationTypeSeeder extends ApplicationExampleSeeder
{
    protected function definition(): array
    {
        return $this->exampleDefinition('Für ein Bootsliegeplatz', 'bootsregistrierung', 'ein Bootsliegeplatz', [
            ['key' => 'boot_name', 'label' => 'Name des Bootes', 'type' => 'text', 'required' => true],
            ['key' => 'bootstyp', 'label' => 'Bootstyp', 'type' => 'text', 'required' => true],
            ['key' => 'bootslaenge_meter', 'label' => 'Bootslänge in Metern', 'type' => 'number', 'required' => true],
            ['key' => 'kennzeichen', 'label' => 'Bootskennzeichen, falls vorhanden', 'type' => 'text', 'required' => false],
            ['key' => 'boot_nachweis', 'label' => 'Bootsnachweis als Anlage', 'type' => 'file', 'required' => false],
        ], [
            ['key' => 'bootsplatz', 'label' => 'Bootsplatz', 'type' => 'text', 'required' => true, 'for_reviewer' => true, 'help_text' => 'Bitte gebe den Bootsplatz für die Bewilligung an.'],
        ], 'Bitte gebe den Bootsplatz für die Bewilligung an.');
    }
}
