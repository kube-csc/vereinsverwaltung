<?php

namespace Database\Seeders;

class ReservierungApplicationTypeSeeder extends ApplicationExampleSeeder
{
    protected function definition(): array
    {
        return $this->exampleDefinition('Reservierung', 'reservierung', 'einer Reservierungsanfrage', [
            ['key' => 'reservierungsobjekt', 'label' => 'Gewünschter Raum oder Gegenstand', 'type' => 'text', 'required' => true],
            ['key' => 'reservierungsdatum', 'label' => 'Gewünschtes Datum', 'type' => 'date', 'required' => true],
            ['key' => 'beginn_zeit', 'label' => 'Gewünschte Beginnzeit', 'type' => 'text', 'required' => true],
            ['key' => 'ende_zeit', 'label' => 'Gewünschte Endzeit', 'type' => 'text', 'required' => true],
            ['key' => 'zweck', 'label' => 'Zweck der Reservierung', 'type' => 'textarea', 'required' => true],
            ['key' => 'teilnehmerzahl', 'label' => 'Voraussichtliche Teilnehmerzahl', 'type' => 'number', 'required' => false],
        ]);
    }
}
