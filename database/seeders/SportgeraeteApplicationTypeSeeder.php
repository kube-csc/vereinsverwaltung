<?php

namespace Database\Seeders;

class SportgeraeteApplicationTypeSeeder extends ApplicationExampleSeeder
{
    protected function definition(): array
    {
        $fields = $this->sharedFields();
        $fields[] = ['key' => 'beantragter_zuschuss', 'label' => 'Beantragte Zuschusshöhe in Euro', 'type' => 'number', 'required' => true];
        $fields[] = ['key' => 'gesamtkosten', 'label' => 'Gesamtkosten in Euro', 'type' => 'number', 'required' => true];
        $fields[] = ['key' => 'sportgeraete_beschreibung', 'label' => 'Beschreibung der Sportgeräte und des geplanten Erwerbs', 'type' => 'textarea', 'required' => true];

        return [
            'name' => 'Förderung Sportgeräte',
            'slug' => 'foerderung-sportgeraete',
            'beschreibung' => 'Förderjahr und verbindliche Abgabefrist müssen vor der Aktivierung noch geklärt werden.',
            'frist' => null,
            'requires_club' => true,
            'requires_funding_pot' => true,
            'aktiv' => false,
            'fields' => $fields,
        ];
    }
}
