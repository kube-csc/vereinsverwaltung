<?php

namespace App\Http\Controllers;

use App\Models\Organiser;
use App\Models\Trainertyp;
use App\Models\Trainertable;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TrainertypAdminController extends Controller
{
    public function index(Request $request): View
    {
        $showDeleted = (bool)$request->boolean('deleted');

        $query = Trainertyp::query()->orderBy('trainerfunktion');

        if ($showDeleted) {
            $query->withTrashed();
        }

        $organisers = Organiser::query()
            ->whereNull('deleted_at')
            ->orderBy('veranstaltung')
            ->get();

        return view('admin.trainertyp.index', [
            'types' => $query->get(),
            'showDeleted' => $showDeleted,
            'organisers' => $organisers,
        ]);
    }

    public function create(): View
    {
        $organisers = Organiser::query()
            ->whereNull('deleted_at')
            ->orderBy('veranstaltung')
            ->get();

        return view('admin.trainertyp.create', [
            'organisers' => $organisers,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'trainerfunktion' => ['required', 'string', 'max:255'],
            'status' => ['required', 'integer', 'in:0,1'],
            'default_sichtbar' => ['required', 'integer', 'in:0,1'],
            'organiser_id' => ['nullable', 'integer', 'exists:organisers,id'],
        ]);

        if (($validated['organiser_id'] ?? null) === 0 || ($validated['organiser_id'] ?? null) === '0') {
            $validated['organiser_id'] = null;
        }

        $type = Trainertyp::create($validated);

        // optional: Meta-Felder, falls du sie später ergänzt
        $type->updated_at = Carbon::now();
        $type->save();

        return redirect()->route('admin.trainertyp.index')
            ->with('success', 'Trainertyp wurde erstellt.');
    }

    public function edit(Trainertyp $trainertyp): View
    {
        $organisers = Organiser::query()
            ->whereNull('deleted_at')
            ->orderBy('veranstaltung')
            ->get();

        return view('admin.trainertyp.edit', [
            'type' => $trainertyp,
            'organisers' => $organisers,
        ]);
    }

    public function update(Request $request, Trainertyp $trainertyp): RedirectResponse
    {
        $validated = $request->validate([
            'trainerfunktion' => ['required', 'string', 'max:255'],
            'status' => ['required', 'integer', 'in:0,1'],
            'default_sichtbar' => ['required', 'integer', 'in:0,1'],
            'organiser_id' => ['nullable', 'integer', 'exists:organisers,id'],
        ]);

        // UI sendet "0" für "keine" -> als NULL speichern
        if (($validated['organiser_id'] ?? null) === 0 || ($validated['organiser_id'] ?? null) === '0') {
            $validated['organiser_id'] = null;
        }

        $oldOrganiserId = $trainertyp->organiser_id;
        $newOrganiserId = $validated['organiser_id'] ?? null;

        $trainertyp->fill($validated);
        $trainertyp->updated_at = Carbon::now();
        $trainertyp->save();

        // Regel (neu): Wenn sich organiser_id ändert, werden bestehende Zuordnungen NICHT angepasst.
        // Statt dessen werden alle Zuordnungen dieses Typs deaktiviert (status=0 + SoftDelete).
        // Grund: bestehende Trainerzuordnungen sollen nicht stillschweigend auf neue Veranstaltung übertragen werden.
        if ((string)$oldOrganiserId !== (string)$newOrganiserId) {
            // Nur aktive Datensätze deaktivieren; bereits soft-gelöschte bleiben unangetastet.
            $toDeactivate = Trainertable::query()
                ->where('trainertyp_id', $trainertyp->id)
                ->whereNull('deleted_at')
                ->get();

            foreach ($toDeactivate as $row) {
                $row->status = 0;
                $row->bearbeiter_id = (int)Auth::id();
                $row->updated_at = Carbon::now();
                $row->save();
                $row->delete(); // SoftDelete
            }

            return back()->with('success', 'Trainertyp wurde gespeichert. Hinweis: Durch die Änderung der Veranstaltung wurden bestehende Zuordnungen deaktiviert.');
        }

        return back()->with('success', 'Trainertyp wurde gespeichert.');
    }

    public function destroy(Trainertyp $trainertyp): RedirectResponse
    {
        // Beim Deaktivieren soll status=0 gesetzt werden (zusätzlich zum SoftDelete)
        $trainertyp->status = 0;
        $trainertyp->updated_at = Carbon::now();
        $trainertyp->save();

        // Wenn eine Trainerfunktion deaktiviert wird, sollen auch alle Zuordnungen (trainertables)
        // dieses Typs deaktiviert werden.
        // Wir markieren sie als inaktiv und soft-löschen sie, damit sie nicht mehr als aktiv gelten.
        $assignments = Trainertable::query()
            ->where('trainertyp_id', $trainertyp->id)
            ->whereNull('deleted_at')
            ->get();

        foreach ($assignments as $assignment) {
            $assignment->status = 0;
            $assignment->bearbeiter_id = (int)Auth::id();
            $assignment->updated_at = Carbon::now();
            $assignment->save();
            $assignment->delete();
        }

        // SoftDelete: Datensätze bleiben erhalten
        $trainertyp->delete();

        return back()->with('success', 'Trainertyp wurde deaktiviert (soft-gelöscht).');
    }

    public function restore(int $trainertyp): RedirectResponse
    {
        $type = Trainertyp::withTrashed()->findOrFail($trainertyp);

        if (!$type->trashed()) {
            return back()->with('success', 'Trainertyp ist bereits aktiv.');
        }

        $type->restore();
        $type->status = 1;
        $type->updated_at = Carbon::now();
        $type->save();

        return back()->with('success', 'Trainertyp wurde reaktiviert.');
    }
}

