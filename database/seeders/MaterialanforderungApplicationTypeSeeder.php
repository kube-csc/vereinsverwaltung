<?php

namespace Database\Seeders;

class MaterialanforderungApplicationTypeSeeder extends ApplicationExampleSeeder
{
    protected function definition(): array
    {
        return $this->exampleDefinition('Materialanforderung', 'materialanforderung', 'einer Materialanforderung', [
            ['key' => 'materialbeschreibung', 'label' => 'Benötigtes Material', 'type' => 'textarea', 'required' => true],
            ['key' => 'menge', 'label' => 'Benötigte Menge', 'type' => 'number', 'required' => true],
            ['key' => 'verwendungszweck', 'label' => 'Verwendungszweck', 'type' => 'textarea', 'required' => true],
            ['key' => 'benoetigt_ab', 'label' => 'Benötigt ab', 'type' => 'date', 'required' => false],
        ]);
    }
}
