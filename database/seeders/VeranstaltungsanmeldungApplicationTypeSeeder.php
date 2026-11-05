<?php

namespace Database\Seeders;

class VeranstaltungsanmeldungApplicationTypeSeeder extends ApplicationExampleSeeder
{
    protected function definition(): array
    {
        return $this->exampleDefinition('Veranstaltungsanmeldung', 'veranstaltungsanmeldung', 'einer Veranstaltungsanmeldung', [
            ['key' => 'veranstaltungsname', 'label' => 'Veranstaltung', 'type' => 'text', 'required' => true],
            ['key' => 'veranstaltungsdatum', 'label' => 'Veranstaltungsdatum', 'type' => 'date', 'required' => true],
            ['key' => 'kontaktperson', 'label' => 'Kontaktperson', 'type' => 'text', 'required' => true],
            ['key' => 'kontakt_email', 'label' => 'E-Mail-Adresse für Rückfragen', 'type' => 'email', 'required' => true],
            ['key' => 'teilnehmerzahl', 'label' => 'Teilnehmerzahl', 'type' => 'number', 'required' => true],
            ['key' => 'anmerkungen', 'label' => 'Weitere Anmerkungen', 'type' => 'textarea', 'required' => false],
        ]);
    }
}
