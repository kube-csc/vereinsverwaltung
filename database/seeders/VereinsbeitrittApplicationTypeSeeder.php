<?php

namespace Database\Seeders;

class VereinsbeitrittApplicationTypeSeeder extends ApplicationExampleSeeder
{
    protected function definition(): array
    {
        return $this->exampleDefinition('Vereinsbeitritt', 'vereinsbeitritt', 'eines Vereinsbeitritts', [
            ['key' => 'vollstaendiger_name', 'label' => 'Vor- und Nachname', 'type' => 'text', 'required' => true],
            ['key' => 'kontakt_email', 'label' => 'E-Mail-Adresse', 'type' => 'email', 'required' => true],
            ['key' => 'geburtsdatum', 'label' => 'Geburtsdatum, falls erforderlich', 'type' => 'date', 'required' => false],
            ['key' => 'gewuenschte_abteilung', 'label' => 'Gewünschte Abteilung oder Sportart', 'type' => 'text', 'required' => false],
            ['key' => 'eintritt_gewuenscht_ab', 'label' => 'Gewünschter Eintrittstermin', 'type' => 'date', 'required' => false],
        ]);
    }
}
