<?php

$werbungOptions = [
    '0'  => 'Nicht ausgewählt',
    '1'  => 'Vereins-Homepage',
    '2'  => 'Event-Homepage',
    '3'  => 'Portalseite',
    '4'  => 'Plakatwerbung',
    '5'  => 'Flyer',
    '6'  => 'Empfehlung durch Sportfreunde',
    '7'  => 'Radio',// inaktiv
    '8'  => 'Drachenboot-Liga',// inaktiv
    '9'  => 'Einladungs-E-Mail',
    '10' => 'Presse',
    '11' => 'Sonstiges',
];

// Ergänze hier alle inaktiven Keys:
$inactiveOptions = ['7', '8'];

return [
    'options'  => $werbungOptions,
    'inactive' => $inactiveOptions,
];
