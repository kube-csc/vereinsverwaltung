<?php

namespace Database\Seeders;

class JugendprojektApplicationTypeSeeder extends ApplicationExampleSeeder
{
    protected function definition(): array
    {
        return [
            'name' => 'Förderung Jugendprojekt',
            'slug' => 'foerderung-jugendprojekt',
            'beschreibung' => 'Förderantrag für ein Jugendprojekt.',
            'frist' => '2026-11-13',
            'requires_club' => true,
            'aktiv' => true,
            'fields' => array_merge($this->sharedFields(), $this->projectFields('Bitte erläutern Sie Art, Inhalt, Umfang und Zeitraum des Jugendprojekts.')),
        ];
    }
}
