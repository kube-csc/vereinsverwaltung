<?php

namespace Database\Seeders;

class ZuschussantragApplicationTypeSeeder extends ApplicationExampleSeeder
{
    protected function definition(): array
    {
        return $this->exampleDefinition('Zuschussantrag', 'zuschussantrag', 'eines Zuschussantrags', [
            ['key' => 'vorhaben', 'label' => 'Vorhaben oder Anschaffung', 'type' => 'text', 'required' => true],
            ['key' => 'beantragter_zuschuss', 'label' => 'Beantragter Zuschuss in Euro', 'type' => 'number', 'required' => true],
            ['key' => 'gesamtkosten', 'label' => 'Gesamtkosten in Euro', 'type' => 'number', 'required' => true],
            ['key' => 'begruendung', 'label' => 'Begründung des Zuschussantrags', 'type' => 'textarea', 'required' => true],
            ['key' => 'kostenplan', 'label' => 'Kostenplan als Anlage', 'type' => 'file', 'required' => false],
        ]);
    }
}
