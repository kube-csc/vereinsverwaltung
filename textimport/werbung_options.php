<?php

$werbungOptions = [
    '0'  => 'Nicht ausgewählt',
    '1'  => 'Vereins-Homepage (kel-datteln.de)',
    '2'  => 'Event Homepage (day-of-dragons.de)',
    '3'  => 'Event Homepage (kanucup-Datteln.de)', // inaktiv
    '4'  => 'Plakatwerbung',
    '5'  => 'Flyer',
    '6'  => 'Empfehlung durch Sportfreunde',
    '7'  => 'Radio',
    '8'  => 'Drachenboot-Liga',
    '9'  => 'Einladungs-E-Mail',
    '10' => 'Presse',
    '11' => 'Sonstiges',
    '12' => 'dragonboat.online',
    '13' => 'lokalkompass.de',
];

// Ergänze hier alle inaktiven Keys:
$inactiveOptions = ['3'];

return [
    'options'  => $werbungOptions,
    'inactive' => $inactiveOptions,
];
