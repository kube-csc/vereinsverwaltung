<?php

namespace Database\Seeders;

class UebungsleiterApplicationTypeSeeder extends ApplicationExampleSeeder
{
    protected function definition(): array
    {
        $fields = $this->sharedFields();
        $fields[] = ['key' => 'uebungsleiter_mit_neuer_qualifikation', 'label' => 'Übungsleiter/innen mit neu erworbenem Übungsleiterschein oder Höherqualifikation', 'type' => 'textarea', 'required' => true];
        $fields[] = ['key' => 'nachweis_qualifikation', 'label' => 'Kopie der neu erworbenen Nachweise', 'type' => 'file', 'required' => true];

        foreach ([
            'verlaengerte_uebungsleiterscheine' => 'Verlängerte Übungsleiterscheine',
            'lsb_gefoerderte_uebungsleiter' => 'Vom Landessportbund geförderte Übungsleiter/innen',
            'nicht_lsb_gefoerderte_uebungsleiter' => 'Nicht vom Landessportbund geförderte Übungsleiter/innen',
            'ehrenamtliche_uebungsleiter_ohne_schein' => 'Ehrenamtliche Übungsleiter/innen ohne Übungsleiterschein',
        ] as $key => $label) {
            $fields[] = ['key' => $key . '_weiblich', 'label' => $label . ' (weiblich)', 'type' => 'number', 'required' => true];
            $fields[] = ['key' => $key . '_maennlich', 'label' => $label . ' (männlich)', 'type' => 'number', 'required' => true];
        }

        return [
            'name' => 'Förderung Übungsleiter',
            'slug' => 'foerderung-uebungsleiter',
            'beschreibung' => 'Förderjahr und verbindliche Abgabefrist müssen vor der Aktivierung noch geklärt werden.',
            'frist' => null,
            'requires_club' => true,
            'aktiv' => false,
            'fields' => $fields,
        ];
    }
}
