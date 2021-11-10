@php
function textmax(&$beschreibung,$sollang,&$abgeschnitten)
{
 $abgeschnitten=0;
 $laenge=strlen($beschreibung);
 if ($laenge>$sollang)
  {
    $beschreibung=substr($beschreibung,0,$sollang);
    $beschreibung=$beschreibung."...";
    $abgeschnitten=1;
  }
}
@endphp

