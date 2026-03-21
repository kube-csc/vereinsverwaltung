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
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TrainerAdminController extends Controller
{
    public function sportSectionsForOrganiser(int $organiser): JsonResponse
    {
        $sportSectionIds = DB::table('organiser_sport_section')
            ->where('organiser_id', $organiser)
            ->pluck('sport_section_id')
            ->map(fn ($v) => (int)$v)
            ->all();

        if (empty($sportSectionIds)) {
            return response()->json([]);
        }

        $sections = SportSection::query()
            ->whereIn('id', $sportSectionIds)
            ->whereNull('deleted_at')
            ->orderBy('abteilung')
            ->get()
            ->map(fn ($s) => [
                'id' => (int)$s->id,
                'label' => trim(($s->abteilung ?? '') . ((isset($s->domain) && $s->domain) ? ' (' . $s->domain . ')' : '')),
            ])
            ->values();

        return response()->json($sections);
    }

    public function index(Request $request): View
    {
        $q = trim((string)$request->get('q', ''));

        $users = User::query()
            // Nur Mitglieder anzeigen, die nicht ausgetreten sind.
            ->where(function ($q) {
                $q->whereNull('vereinsaustritt')
                    ->orWhere('vereinsaustritt', '>', Carbon::today());
            })
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

        $organisers = Organiser::query()
            ->whereNull('deleted_at')
            ->orderBy('veranstaltung')
            ->get();

        $activeAssignments = Trainertable::query()
            ->with(['trainertyp', 'organiser', 'sportSection'])
            ->where('user_id', $user->id)
            ->whereNull('deleted_at')
            ->orderBy('trainertyp_id')
            ->get();

        $inactiveAssignments = Trainertable::query()
            ->with(['trainertyp', 'organiser', 'sportSection'])
            ->where('user_id', $user->id)
            ->onlyTrashed()
            ->orderByDesc('deleted_at')
            ->get();

        return view('admin.trainer.edit', [
            'user' => $user,
            'trainertyps' => $trainertyps,
            'activeAssignments' => $activeAssignments,
            'inactiveAssignments' => $inactiveAssignments,
            'organisers' => $organisers,
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
            'sportSection_id' => ['nullable', 'integer', 'exists:sport_sections,id'],
        ]);

        $trainertypIds = collect($validated['trainertyp_ids'] ?? [])
            ->map(fn ($v) => (int)$v)
            ->unique()
            ->values();

        if ($trainertypIds->isEmpty()) {
            return back()->with('success', 'Keine Änderungen gespeichert.');
        }

        foreach ($trainertypIds as $trainertypId) {
            $typ = Trainertyp::query()
                ->where('status', 1)
                ->findOrFail((int)$trainertypId);

            $organiserId = (int)($typ->organiser_id ?? 0);
            $sportSectionIdInput = (int)($validated['sportSection_id'] ?? 0);

            // Wenn der Trainertyp keine Veranstaltung hat, darf auch keine Abteilung gespeichert werden.
            if ($organiserId <= 0) {
                $sportSectionIdInput = 0;
            } else {
                // SportSection muss zur Veranstaltung (organiser) passen (organiser_sport_section).
                if ($sportSectionIdInput > 0) {
                    $exists = DB::table('organiser_sport_section')
                        ->where('organiser_id', $organiserId)
                        ->where('sport_section_id', $sportSectionIdInput)
                        ->exists();

                    if (!$exists) {
                        return back()
                            ->withErrors(['sportSection_id' => 'Die ausgewählte Abteilung gehört nicht zur ausgewählten Veranstaltung.'])
                            ->withInput();
                    }
                }
            }

            // Wenn bereits eine AKTIVE Zuordnung mit gleicher Abteilung existiert,
            // dann nichts neu anlegen (Duplikat vermeiden).
            // WICHTIG: Es kann mehrere Zuordnungen pro User+Trainertyp geben,
            // solange sich die Abteilung (sportSection_id) unterscheidet.
            $alreadyActiveSameSection = Trainertable::query()
                ->where('user_id', $user->id)
                ->where('trainertyp_id', $trainertypId)
                ->where('sportSection_id', $sportSectionIdInput)
                ->whereNull('deleted_at')
                ->exists();

            if ($alreadyActiveSameSection) {
                continue;
            }

            // Wiederverwendung von soft-gelöschten Datensätzen zur Reduktion unnötiger DB-Einträge.
            // Priorität:
            //  1) exakt passende deaktivierte Zuordnung des Users (gleicher Typ + gleiche Abteilung)
            //  2) sonst irgendeine deaktivierte Zuordnung des Users ("Recycling" -> Typ/Abteilung werden umgehängt)
            //  3) sonst irgendeine deaktivierte Zuordnung ("Recycling" über alle User)
            //  4) sonst neu anlegen
            // Beim Recycling gilt fachlich: "Neuanlage" (Store) => created_at + autor_id neu setzen.
            $reuseCandidate = Trainertable::query()
                ->onlyTrashed()
                ->where('user_id', $user->id)
                ->where('trainertyp_id', $trainertypId)
                ->where('sportSection_id', $sportSectionIdInput)
                ->latest('deleted_at')
                ->first();

            if (!$reuseCandidate) {
                $reuseCandidate = Trainertable::query()
                    ->onlyTrashed()
                    ->where('user_id', $user->id)
                    ->latest('deleted_at')
                    ->first();
            }

            if (!$reuseCandidate) {
                $reuseCandidate = Trainertable::query()
                    ->onlyTrashed()
                    ->latest('deleted_at')
                    ->first();
            }

            if ($reuseCandidate) {
                // Restore + Umhängen auf Zielkombination.
                $reuseCandidate->restore();
                $reuseCandidate->status = 1;
                $reuseCandidate->sichtbar = (int)($typ->default_sichtbar ?? 1);
                $reuseCandidate->user_id = $user->id;
                $reuseCandidate->trainertyp_id = (int)$trainertypId;
                $reuseCandidate->organiser_id = $organiserId;
                $reuseCandidate->sportSection_id = $sportSectionIdInput;
                $reuseCandidate->autor_id = (int)Auth::id();
                $reuseCandidate->bearbeiter_id = (int)Auth::id();
                $reuseCandidate->created_at = Carbon::now();
                $reuseCandidate->updated_at = Carbon::now();
                $reuseCandidate->save();
                continue;
            }

            Trainertable::create([
                'user_id' => $user->id,
                'trainertyp_id' => (int)$trainertypId,
                'sportSection_id' => $sportSectionIdInput,
                'organiser_id' => $organiserId,
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
        // Reaktivierung aus der Benutzer-Detailansicht (/admin/trainer/{user}).
        // Unterschied zu reactivateFromTypes():
        // - Hier wird die Zuordnung IM KONTEXT EINES BESTIMMTEN USERS reaktiviert.
        // - Wir filtern extra auf user_id, damit keine fremde Zuordnung reaktiviert werden kann.
        // - Redirect geht zurück auf die User-Detailseite.
        $trainertable = Trainertable::withTrashed()
            ->where('user_id', $user->id)
            ->findOrFail($trainertable);

        if (!$trainertable->trashed()) {
            return back()->with('success', 'Zuordnung ist bereits aktiv.');
        }

        $trainertable->restore();
        $trainertable->status = 1;
        // Reaktivieren ist NICHT die "Neuanlage" (Store), sondern nur das Wieder-Aktivieren.
        // Daher bleiben autor_id/created_at unverändert.
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
        // Reaktivierung aus der Trainertyp-Übersicht (/admin/trainer-typen).
        // Unterschied zu reactivate():
        // - Hier gibt es keinen User-Parameter, weil die Aktion aus einer globalen Übersicht kommt.
        // - Es wird nur anhand der trainertable-ID reaktiviert.
        // - Redirect geht zurück auf die Übersicht.
        $assignment = Trainertable::withTrashed()->findOrFail($trainertable);

        if (!$assignment->trashed()) {
            return back()->with('success', 'Zuordnung ist bereits aktiv.');
        }

        $assignment->restore();
        $assignment->status = 1;
        // Reaktivieren ist NICHT die "Neuanlage" (Store), sondern nur das Wieder-Aktivieren.
        // Daher bleiben autor_id/created_at unverändert.
        $assignment->bearbeiter_id = (int)Auth::id();
        $assignment->updated_at = Carbon::now();
        $assignment->save();

        return back()->with('success', 'Trainerfunktion wurde reaktiviert.');
    }
}

