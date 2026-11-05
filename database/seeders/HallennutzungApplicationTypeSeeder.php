<?php

namespace Database\Seeders;

class HallennutzungApplicationTypeSeeder extends ApplicationExampleSeeder
{
    protected function definition(): array
    {
        return $this->exampleDefinition('Hallennutzung', 'hallennutzung', 'einer Hallennutzung', [
            ['key' => 'anlass', 'label' => 'Anlass der Hallennutzung', 'type' => 'text', 'required' => true],
            ['key' => 'nutzungsdatum', 'label' => 'Gewünschtes Nutzungsdatum', 'type' => 'date', 'required' => true],
            ['key' => 'beginn_zeit', 'label' => 'Gewünschte Beginnzeit', 'type' => 'text', 'required' => true, 'help_text' => 'Bitte eine Uhrzeit angeben, zum Beispiel 18:00.'],
            ['key' => 'ende_zeit', 'label' => 'Gewünschte Endzeit', 'type' => 'text', 'required' => true, 'help_text' => 'Bitte eine Uhrzeit angeben, zum Beispiel 20:00.'],
            ['key' => 'kontaktperson', 'label' => 'Kontaktperson', 'type' => 'text', 'required' => true],
            ['key' => 'kontakt_email', 'label' => 'E-Mail-Adresse für Rückfragen', 'type' => 'email', 'required' => true],
            ['key' => 'teilnehmerzahl', 'label' => 'Voraussichtliche Teilnehmerzahl', 'type' => 'number', 'required' => false],
        ]);
    }
}
