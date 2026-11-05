<?php

namespace Database\Seeders;

class BootsregistrierungApplicationTypeSeeder extends ApplicationExampleSeeder
{
    protected function definition(): array
    {
        return $this->exampleDefinition('Bootsregistrierung', 'bootsregistrierung', 'einer Bootsregistrierung', [
            ['key' => 'boot_name', 'label' => 'Name des Bootes', 'type' => 'text', 'required' => true],
            ['key' => 'bootstyp', 'label' => 'Bootstyp', 'type' => 'text', 'required' => true],
            ['key' => 'bootslaenge_meter', 'label' => 'Bootslänge in Metern', 'type' => 'number', 'required' => true],
            ['key' => 'kennzeichen', 'label' => 'Bootskennzeichen, falls vorhanden', 'type' => 'text', 'required' => false],
            ['key' => 'boot_nachweis', 'label' => 'Bootsnachweis als Anlage', 'type' => 'file', 'required' => false],
        ]);
    }
}
