<?php

namespace App\Http\Controllers;

use App\Models\eventGroup;
use Auth;
use Illuminate\Support\Facades\Storage;

use Illuminate\Http\Request;

class EventGroupController extends Controller
{
    public function aktiv($sportSection_id)
    {
        eventGroup::find($sportSection_id)->update([
            'visible'  => '2',
            'bearbeiter_id' => Auth::user()->id,
        ]);
        return Redirect()->back()->with('success' , 'Event Gruppe wurde sichtbar geschaltet.');
    }

    public function inaktiv($sportSection_id)
    {
        eventGroup::find($sportSection_id)->update([
            'visible'  => '0',
            'bearbeiter_id' => Auth::user()->id,
        ]);
        return Redirect()->back()->with('success' , 'Event Gruppe wurde unsichtbar geschaltet.');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory
     */
    public function index()
    {
        $eventGroups = eventGroup::query()
            ->withCount('events')
            ->orderBy('termingruppe')
            ->paginate(5);
        return view('admin.eventGroup.index' , compact('eventGroups'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory
     */
    public function create()
    {
        return view('admin.eventGroup.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        // Hostname-Regel: erlaubt z.B. example.de oder sub.example.de (ohne Protokoll, ohne Port, ohne Pfad)
        $hostnameRule = ['nullable', 'string', 'max:255', 'regex:/^(?=.{1,253}$)(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?$/i'];

        $validated = $request->validate([
            'termingruppe' => 'required|max:50',
            'domain'  => $hostnameRule,
            'liveDomain' => $hostnameRule,
            'headerTitel' => 'nullable|string|max:255',
            'headerSlogen' => 'nullable|string|max:255',
            'headerBild' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            // Optional: Hex-Farbe (#RGB, #RRGGBB oder #RRGGBBAA)
            'accentColor' => ['nullable', 'regex:/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6}|[0-9a-fA-F]{8})$/'],
        ]);

        $eventGroup= new eventGroup(
            [
                'termingruppe' => $request->termingruppe,
                'domain' => $validated['domain'] ?? null,
                'liveDomain' => $validated['liveDomain'] ?? null,
                'headerTitel' => $validated['headerTitel'] ?? null,
                'headerSlogen' => $validated['headerSlogen'] ?? null,
                'accentColor' => !empty($validated['accentColor'] ?? null) ? $validated['accentColor'] : null,
                'user_id'  => Auth::user()->id,
                'bearbeiter_id' => Auth::user()->id,
            ]
        );
        $eventGroup->save();

        // Headerbild-Upload (analog zu Instruction: Dateiname mit ID + Timestamp)
        // Hinweis: In der DB wird NUR der Dateiname gespeichert. Der Ordner "groupEventHeader" ist fest verdrahtet.
        if ($request->hasFile('headerBild')) {
            $file = $request->file('headerBild');
            $ext = strtolower($file->getClientOriginalExtension() ?: 'jpg');
            $fileName = 'eventgroup_header_' . $eventGroup->id . '_' . time() . '.' . $ext;
            $file->storeAs('groupEventHeader', $fileName, 'public');

            $eventGroup->headerBild = $fileName;
            $eventGroup->save();
        }

        return redirect('/Eventgruppe/alle')->with(
            [
                'success' => 'Die Event Gruppe <b>' . $request->termingruppe . '</b> wurde angelegt.'
            ]
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\eventGroup  $eventGroup
     * @return \Illuminate\Http\Response
     */
    public function show(eventGroup $eventGroup)
    {
        return response()->noContent();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\eventGroup  $eventGroup
     * @return \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory
     */
    public function edit($eventGroup_id)
    {
        $eventGroup =eventGroup::find($eventGroup_id);
        return view('admin.eventGroup.edit',compact('eventGroup'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\eventGroup  $eventGroup
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, $eventGroup_id)
    {
        $eventGroup = eventGroup::findOrFail($eventGroup_id);

        // Hostname-Regel: erlaubt z.B. example.de oder sub.example.de (ohne Protokoll, ohne Port, ohne Pfad)
        $hostnameRule = ['nullable', 'string', 'max:255', 'regex:/^(?=.{1,253}$)(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?$/i'];

        $validated = $request->validate([
            'termingruppe' => 'required|max:50',
            'domain'       => $hostnameRule,
            'liveDomain'   => $hostnameRule,
            'headerTitel'  => 'nullable|string|max:255',
            'headerSlogen' => 'nullable|string|max:255',
            'headerBild'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            // Optional: Hex-Farbe (#RGB oder #RRGGBB)
            'accentColor'  => ['nullable', 'regex:/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6}|[0-9a-fA-F]{8})$/'],
            'accentColor_reset' => 'nullable|boolean',
            'headerBild_remove' => 'nullable|boolean',
        ]);

        $update = [
            'termingruppe'    => $request->termingruppe,
            'domain'          => $validated['domain'] ?? null,
            'liveDomain'      => $validated['liveDomain'] ?? null,
            'headerTitel'     => $validated['headerTitel'] ?? null,
            'headerSlogen'    => $validated['headerSlogen'] ?? null,
            'bearbeiter_id'   => Auth::user()->id,
        ];

        // Akzentfarbe: leer/Reset => NULL
        if ($request->boolean('accentColor_reset')) {
            $update['accentColor'] = null;
        } else {
            $update['accentColor'] = !empty($validated['accentColor'] ?? null)
                ? $validated['accentColor']
                : null;
        }

        // Headerbild-Logik:
        // - Upload überschreibt das bestehende Bild.
        // - Entfernen löscht die Datei (falls vorhanden) und setzt DB-Feld auf NULL.
        if ($request->hasFile('headerBild')) {
            $file = $request->file('headerBild');
            $ext = strtolower($file->getClientOriginalExtension() ?: 'jpg');
            $fileName = 'eventgroup_header_' . $eventGroup->id . '_' . time() . '.' . $ext;
            $file->storeAs('groupEventHeader', $fileName, 'public');

            if (!empty($eventGroup->headerBild)) {
                $oldPath = $eventGroup->headerBild;
                // Abwärtskompatibilität: frühere Versionen haben ggf. nur den Dateinamen gespeichert.
                if (!str_contains($oldPath, '/')) {
                    $oldPath = 'groupEventHeader/' . $oldPath;
                }
                Storage::disk('public')->delete($oldPath);
            }

            // In der DB wird NUR der Dateiname gespeichert. Der Ordner ist fest verdrahtet.
            $update['headerBild'] = $fileName;
        } elseif ($request->boolean('headerBild_remove')) {
            if (!empty($eventGroup->headerBild)) {
                $oldPath = $eventGroup->headerBild;
                if (!str_contains($oldPath, '/')) {
                    $oldPath = 'groupEventHeader/' . $oldPath;
                }
                Storage::disk('public')->delete($oldPath);
            }
            $update['headerBild'] = null;
        }

        $eventGroup->update($update);

        return redirect('/Eventgruppe/alle')->with(
            [
                'success' => 'Die Daten von der Event Gruppe <b>' . $request->termingruppe . '</b> wurden geändert.'
            ]
        );

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\eventGroup  $eventGroup
     * @return \Illuminate\Http\Response
     */
    public function destroy(eventGroup $eventGroup)
    {
        return response()->noContent();
    }

    public function softDelete($eventGroup_id)
    {
        $delete = eventGroup::find($eventGroup_id)->delete();
        return redirect('/Eventgruppe/alle')->with(
            [
                'success' => 'Die Event Gruppe wurde gelöscht.'
            ]
        );
    }
}
