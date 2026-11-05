<?php

namespace Database\Seeders;

class WohnwagenstellplatzApplicationTypeSeeder extends ApplicationExampleSeeder
{
    protected function definition(): array
    {
        return $this->exampleDefinition('Wohnwagenstellplatz', 'wohnwagenstellplatz', 'einer Wohnwagenstellplatzanfrage', [
            ['key' => 'anreise', 'label' => 'Anreisedatum', 'type' => 'date', 'required' => true],
            ['key' => 'abreisedatum', 'label' => 'Abreisedatum', 'type' => 'date', 'required' => true],
            ['key' => 'wohnwagen_laenge_meter', 'label' => 'Länge des Wohnwagens in Metern', 'type' => 'number', 'required' => false],
            ['key' => 'kennzeichen', 'label' => 'Kennzeichen, falls für die Zuordnung erforderlich', 'type' => 'text', 'required' => false],
            ['key' => 'kontakt_email', 'label' => 'E-Mail-Adresse für Rückfragen', 'type' => 'email', 'required' => true],
        ]);
    }
}
