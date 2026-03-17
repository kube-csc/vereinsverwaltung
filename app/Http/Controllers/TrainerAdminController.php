<?php

namespace App\Http\Controllers;

use App\Models\Trainertable;
use App\Models\Trainertyp;
use App\Models\User;
use App\Models\Organiser;
use App\Models\SportSection;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TrainerAdminController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string)$request->get('q', ''));

        $users = User::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('vorname', 'like', '%' . $q . '%')
                        ->orWhere('nachname', 'like', '%' . $q . '%')
                        ->orWhere('email', 'like', '%' . $q . '%');
                });
            })
            ->orderBy('nachname')
            ->orderBy('vorname')
            ->paginate(25)
            ->withQueryString();

        return view('admin.trainer.index', [
            'users' => $users,
            'q' => $q,
        ]);
    }

    public function edit(User $user): View
    {
        $trainertyps = Trainertyp::query()
            ->where('status', 1)
            ->orderBy('trainerfunktion')
            ->get();

        $activeAssignments = Trainertable::query()
            ->with('trainertyp')
            ->where('user_id', $user->id)
            ->whereNull('deleted_at')
            ->orderBy('trainertyp_id')
            ->get();

        $inactiveAssignments = Trainertable::query()
            ->with('trainertyp')
            ->where('user_id', $user->id)
            ->onlyTrashed()
            ->orderByDesc('deleted_at')
            ->get();

        return view('admin.trainer.edit', [
            'user' => $user,
            'trainertyps' => $trainertyps,
            'activeAssignments' => $activeAssignments,
            'inactiveAssignments' => $inactiveAssignments,
        ]);
    }

    /**
     * Speichert die Zuordnung. Mehrfachauswahl möglich.
     * Vorhandene Zuordnungen werden NICHT gelöscht; neue werden ergänzt.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'trainertyp_ids' => ['nullable', 'array'],
            'trainertyp_ids.*' => ['integer', 'exists:trainertyps,id'],
        ]);

        $trainertypIds = collect($validated['trainertyp_ids'] ?? [])
            ->map(fn ($v) => (int)$v)
            ->unique()
            ->values();

        if ($trainertypIds->isEmpty()) {
            return back()->with('success', 'Keine Änderungen gespeichert.');
        }

        $existingActive = Trainertable::query()
            ->where('user_id', $user->id)
            ->whereNull('deleted_at')
            ->pluck('trainertyp_id')
            ->map(fn ($v) => (int)$v)
            ->all();

        $toAdd = $trainertypIds->diff($existingActive);

        foreach ($toAdd as $trainertypId) {
            $typ = Trainertyp::query()
                ->where('status', 1)
                ->findOrFail((int)$trainertypId);

            // Reaktivieren, falls schon mal soft-gelöscht existiert
            $existingTrashed = Trainertable::query()
                ->onlyTrashed()
                ->where('user_id', $user->id)
                ->where('trainertyp_id', $trainertypId)
                ->latest('deleted_at')
                ->first();

            if ($existingTrashed) {
                $existingTrashed->restore();
                $existingTrashed->status = 1;
                $existingTrashed->bearbeiter_id = (int)Auth::id();
                $existingTrashed->updated_at = Carbon::now();
                $existingTrashed->save();
                continue;
            }

            Trainertable::create([
                'user_id' => $user->id,
                'trainertyp_id' => (int)$trainertypId,
                'sportSection_id' => (int)($typ->default_sportSection_id ?? 0),
                'organiser_id' => (int)($typ->default_organiser_id ?? 0),
                'status' => 1,
                'sichtbar' => (int)($typ->default_sichtbar ?? 1),
                'autor_id' => (int)Auth::id(),
                'bearbeiter_id' => (int)Auth::id(),
            ]);
        }

        return back()->with('success', 'Trainerfunktionen wurden gespeichert.');
    }

    public function deactivate(User $user, int $trainertable): RedirectResponse
    {
        $trainertable = Trainertable::query()->where('user_id', $user->id)->findOrFail($trainertable);

        $trainertable->status = 0;
        $trainertable->bearbeiter_id = (int)Auth::id();
        $trainertable->updated_at = Carbon::now();
        $trainertable->save();

        $trainertable->delete(); // SoftDelete

        return back()->with('success', 'Trainerfunktion wurde deaktiviert.');
    }

    public function toggleVisible(User $user, int $trainertable): RedirectResponse
    {
        $trainertable = Trainertable::query()
            ->where('user_id', $user->id)
            ->whereNull('deleted_at')
            ->findOrFail($trainertable);

        $trainertable->sichtbar = (int)!((int)$trainertable->sichtbar === 1);
        $trainertable->bearbeiter_id = (int)Auth::id();
        $trainertable->updated_at = Carbon::now();
        $trainertable->save();

        return back()->with('success', 'Sichtbarkeit wurde aktualisiert.');
    }

    public function reactivate(User $user, int $trainertable): RedirectResponse
    {
        $trainertable = Trainertable::withTrashed()
            ->where('user_id', $user->id)
            ->findOrFail($trainertable);

        if (!$trainertable->trashed()) {
            return back()->with('success', 'Zuordnung ist bereits aktiv.');
        }

        $trainertable->restore();
        $trainertable->status = 1;
        $trainertable->bearbeiter_id = (int)Auth::id();
        $trainertable->updated_at = Carbon::now();
        $trainertable->save();

        return back()->with('success', 'Trainerfunktion wurde reaktiviert.');
    }

    public function typesIndex(Request $request): View
    {
        $showInactive = (bool)$request->boolean('inactive');

        $trainertyps = Trainertyp::query()
            ->orderBy('trainerfunktion')
            ->get();

        // Zuordnungen (aktiv + optional soft-deleted)
        $assignmentsQuery = Trainertable::query()
            ->with(['user', 'trainertyp', 'organiser', 'sportSection'])
            ->when($showInactive, fn ($q) => $q->withTrashed(), fn ($q) => $q->whereNull('deleted_at'))
            ->orderBy('trainertyp_id')
            ->orderBy('user_id');

        $assignments = $assignmentsQuery->get();

        // Gruppierung nach Typ
        $grouped = $assignments->groupBy('trainertyp_id');

        // Für Klartext-Ausgabe der Einstellungen pro Trainertyp
        $organisers = Organiser::query()
            ->whereNull('deleted_at')
            ->orderBy('veranstaltung')
            ->get();

        $sportSections = SportSection::query()
            ->whereNull('deleted_at')
            ->orderBy('abteilung')
            ->get();

        return view('admin.trainer.types', [
            'trainertyps' => $trainertyps,
            'groupedAssignments' => $grouped,
            'showInactive' => $showInactive,
            'organisers' => $organisers,
            'sportSections' => $sportSections,
        ]);
    }

    public function deactivateFromTypes(int $trainertable): RedirectResponse
    {
        $assignment = Trainertable::query()->findOrFail($trainertable);

        $assignment->status = 0;
        $assignment->bearbeiter_id = (int)Auth::id();
        $assignment->updated_at = Carbon::now();
        $assignment->save();
        $assignment->delete();

        return back()->with('success', 'Trainerfunktion wurde deaktiviert.');
    }

    public function toggleVisibleFromTypes(int $trainertable): RedirectResponse
    {
        $assignment = Trainertable::query()
            ->whereNull('deleted_at')
            ->findOrFail($trainertable);

        $assignment->sichtbar = (int)!((int)$assignment->sichtbar === 1);
        $assignment->bearbeiter_id = (int)Auth::id();
        $assignment->updated_at = Carbon::now();
        $assignment->save();

        return back()->with('success', 'Sichtbarkeit wurde aktualisiert.');
    }

    public function reactivateFromTypes(int $trainertable): RedirectResponse
    {
        $assignment = Trainertable::withTrashed()->findOrFail($trainertable);

        if (!$assignment->trashed()) {
            return back()->with('success', 'Zuordnung ist bereits aktiv.');
        }

        $assignment->restore();
        $assignment->status = 1;
        $assignment->bearbeiter_id = (int)Auth::id();
        $assignment->updated_at = Carbon::now();
        $assignment->save();

        return back()->with('success', 'Trainerfunktion wurde reaktiviert.');
    }
}

