<?php

namespace App\Helpers;

use App\Models\Race;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class RaceTimeHelper
{
    public static function timeVerschiebung($race_id, $rennUhrzeit, $zeit, $zeitMinAbstand)
    {
        $race = Race::find($race_id);
        if (!$race) {
            return;
        }

        $letzteStartArr = explode(":", $rennUhrzeit);
        $letzteStartMin = ((int)$letzteStartArr[0]) * 60 + ((int)$letzteStartArr[1]);

        $raceTimes = Race::where('event_id', Session::get('regattaSelectId'))
            ->where('id', '!=', $race_id)
            ->where('rennUhrzeit', '>', $race->rennUhrzeit)
            ->where('rennDatum', $race->rennDatum)
            ->orderBy('rennUhrzeit')
            ->get();

        $aufholProRennen = (int)$zeit;
        $minAbstand = (int)$zeitMinAbstand;

        $verspaetungAktiv = true;

        foreach ($raceTimes as $raceTime) {
            $nextOrigArr = explode(":", $raceTime->rennUhrzeit);
            $nextOrigMin = ((int)$nextOrigArr[0]) * 60 + ((int)$nextOrigArr[1]);

            if (!$verspaetungAktiv) {
                if ($raceTime->verspaetungUhrzeit !== $raceTime->rennUhrzeit) {
                    Race::find($raceTime->id)->update([
                        'verspaetungUhrzeit' => $raceTime->rennUhrzeit,
                        'bearbeiter_id'      => Auth::id(),
                        'updated_at'         => Carbon::now()
                    ]);
                }
                $letzteStartMin = $nextOrigMin;
                continue;
            }

            $neuerStartMin = $letzteStartMin + $minAbstand;
            $aufholMoeglich = min($aufholProRennen, max(0, $nextOrigMin - $neuerStartMin));
            $berechneteStartMin = $nextOrigMin - $aufholMoeglich;

            if ($berechneteStartMin < $letzteStartMin + $minAbstand) {
                $berechneteStartMin = $letzteStartMin + $minAbstand;
            }
            if ($berechneteStartMin < $letzteStartMin) {
                $berechneteStartMin = $letzteStartMin;
            }
            if ($berechneteStartMin < $nextOrigMin) {
                $rennUhrzeitStr = $raceTime->rennUhrzeit;
                if ($raceTime->verspaetungUhrzeit !== $rennUhrzeitStr) {
                    Race::find($raceTime->id)->update([
                        'verspaetungUhrzeit' => $rennUhrzeitStr,
                        'bearbeiter_id'            => Auth::id(),
                        'updated_at'               => Carbon::now()
                    ]);
                }
                $letzteStartMin = $nextOrigMin;
                $verspaetungAktiv = false;
                continue;
            }

            $neueStunde = floor($berechneteStartMin / 60);
            $neueMinute = $berechneteStartMin % 60;
            $neueStartzeit = sprintf('%02d:%02d:00', $neueStunde, $neueMinute);

            Race::find($raceTime->id)->update([
                'verspaetungUhrzeit' => $neueStartzeit,
                'bearbeiter_id'            => Auth::id(),
                'updated_at'               => Carbon::now()
            ]);

            $letzteStartMin = $berechneteStartMin;
        }
    }
}

