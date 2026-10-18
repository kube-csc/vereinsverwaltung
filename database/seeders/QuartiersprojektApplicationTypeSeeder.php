<?php

namespace Database\Seeders;

class QuartiersprojektApplicationTypeSeeder extends ApplicationExampleSeeder
{
    protected function definition(): array
    {
        return [
            'name' => 'Öffentlichkeitswirksames Projekt in den Quartieren',
            'slug' => 'oeffentlichkeitswirksames-quartiersprojekt',
            'beschreibung' => 'Förderantrag für ein öffentlichkeitswirksames Projekt in den Quartieren mit Bezug zum Pakt für den Sport.',
            'frist' => '2026-11-13',
            'requires_club' => true,
            'aktiv' => true,
            'fields' => array_merge($this->sharedFields(), $this->projectFields('Bitte erläutern Sie den Bezug zum Pakt für den Sport sowie Art, Inhalt, Umfang und Zeitraum des Projekts.')),
        ];
    }
}
