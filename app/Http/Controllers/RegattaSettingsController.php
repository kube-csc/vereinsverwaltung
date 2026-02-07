<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class RegattaSettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function edit()
    {
        $eventId = Session::get('regattaSelectId');
        if (!$eventId) {
            return redirect('/Regattamenu')->with([
                'success' => 'Bitte zuerst eine Regatta auswählen.'
            ]);
        }

        $event = Event::find($eventId);

        $teilnehmermaxOptions = [
            0 => 'unbegrenzt',
            1 => 'max. Teilnehmerzahl, keine Meldung möglich, keine Warteliste',
            2 => 'max. Teilnehmerzahl mit Warteliste',
            3 => 'max. Teilnehmerzahl mit Warteliste + automatische Bahnauffüllung',
        ];

        return view('regattaManagement.regattaSettings.edit')->with([
            'event' => $event,
            'teilnehmermaxOptions' => $teilnehmermaxOptions,
        ]);
    }

    public function update(Request $request)
    {
        $eventId = Session::get('regattaSelectId');
        if (!$eventId) {
            return redirect('/Regattamenu')->with([
                'success' => 'Bitte zuerst eine Regatta auswählen.'
            ]);
        }

        $validated = $request->validate([
            'teilnehmer' => ['nullable', 'integer', 'min:0'],
            'teilnehmermax' => ['required', 'integer', 'in:0,1,2,3'],
        ]);

        Event::findOrFail($eventId)->update([
            'teilnehmer' => (int) ($validated['teilnehmer'] ?? 0),
            'teilnehmermax' => (int) $validated['teilnehmermax'],
            'bearbeiter_id' => Auth::id(),
            'updated_at' => Carbon::now(),
        ]);

        return redirect('/Regatta/Einstellungen')->with([
            'success' => 'Regatta Einstellungen wurden gespeichert.'
        ]);
    }
}
