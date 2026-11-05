<?php

namespace Database\Seeders;

class LiegeplatzApplicationTypeSeeder extends ApplicationExampleSeeder
{
    protected function definition(): array
    {
        return $this->exampleDefinition('Liegeplatz', 'liegeplatz', 'einer Liegeplatzanfrage', [
            ['key' => 'boot_name', 'label' => 'Name des Bootes', 'type' => 'text', 'required' => true],
            ['key' => 'bootstyp', 'label' => 'Bootstyp', 'type' => 'text', 'required' => true],
            ['key' => 'bootslaenge_meter', 'label' => 'Bootslänge in Metern', 'type' => 'number', 'required' => true],
            ['key' => 'gewuenschter_zeitraum', 'label' => 'Gewünschter Liegezeitraum', 'type' => 'textarea', 'required' => true],
            ['key' => 'kontakt_email', 'label' => 'E-Mail-Adresse für Rückfragen', 'type' => 'email', 'required' => true],
        ]);
    }
}
