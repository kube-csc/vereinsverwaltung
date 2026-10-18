<?php

namespace Database\Seeders;

class IntegrationApplicationTypeSeeder extends ApplicationExampleSeeder
{
    protected function definition(): array
    {
        return [
            'name' => 'Förderung Integration geflüchteter Erwachsener',
            'slug' => 'foerderung-integration-gefluechteter-erwachsener',
            'beschreibung' => 'Förderantrag für ein Projekt zur Integration geflüchteter Erwachsener.',
            'frist' => '2026-11-13',
            'requires_club' => true,
            'requires_funding_pot' => true,
            'aktiv' => true,
            'fields' => array_merge($this->sharedFields(), $this->projectFields('Stellen Sie den Bezug zur Förderung der Integration geflüchteter Erwachsener dar.')),
        ];
    }
}
