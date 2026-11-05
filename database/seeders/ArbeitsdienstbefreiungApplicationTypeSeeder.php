<?php

namespace Database\Seeders;

class ArbeitsdienstbefreiungApplicationTypeSeeder extends ApplicationExampleSeeder
{
    protected function definition(): array
    {
        return $this->exampleDefinition('Arbeitsdienstbefreiung', 'arbeitsdienstbefreiung', 'einer Arbeitsdienstbefreiung', [
            ['key' => 'arbeitsdienst_datum', 'label' => 'Datum des Arbeitsdienstes', 'type' => 'date', 'required' => true],
            ['key' => 'begruendung', 'label' => 'Begründung des Antrags', 'type' => 'textarea', 'required' => true],
            ['key' => 'nachweis', 'label' => 'Begründung als Anlage', 'type' => 'file', 'required' => false],
        ]);
    }
}
