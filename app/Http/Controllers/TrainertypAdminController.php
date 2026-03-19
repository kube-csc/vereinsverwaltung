<?php

namespace App\Http\Controllers;

use App\Models\Organiser;
use App\Models\SportSection;
use App\Models\Trainertyp;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TrainertypAdminController extends Controller
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
        $showDeleted = (bool)$request->boolean('deleted');

        $query = Trainertyp::query()->orderBy('trainerfunktion');

        if ($showDeleted) {
            $query->withTrashed();
        }

        $organisers = Organiser::query()
            ->whereNull('deleted_at')
            ->orderBy('veranstaltung')
            ->get();

        $sportSections = SportSection::query()
            ->whereNull('deleted_at')
            ->orderBy('abteilung')
            ->get();

        return view('admin.trainertyp.index', [
            'types' => $query->get(),
            'showDeleted' => $showDeleted,
            'organisers' => $organisers,
            'sportSections' => $sportSections,
        ]);
    }

    public function create(): View
    {
        $organisers = Organiser::query()
            ->whereNull('deleted_at')
            ->orderBy('veranstaltung')
            ->get();

        $selectedOrganiserId = (int)old('default_organiser_id', $organisers->first()->id ?? 0);

        $sportSections = collect();
        if ($selectedOrganiserId > 0) {
            $sportSectionIds = DB::table('organiser_sport_section')
                ->where('organiser_id', $selectedOrganiserId)
                ->pluck('sport_section_id')
                ->map(fn ($v) => (int)$v)
                ->all();

            if (!empty($sportSectionIds)) {
                $sportSections = SportSection::query()
                    ->whereIn('id', $sportSectionIds)
                    ->whereNull('deleted_at')
                    ->orderBy('abteilung')
                    ->get();
            }
        }

        return view('admin.trainertyp.create', [
            'organisers' => $organisers,
            'sportSections' => $sportSections,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'trainerfunktion' => ['required', 'string', 'max:255'],
            'status' => ['required', 'integer', 'in:0,1'],
            'default_sichtbar' => ['required', 'integer', 'in:0,1'],
            'default_organiser_id' => ['nullable', 'integer', 'exists:organisers,id'],
            'default_sportSection_id' => ['nullable', 'integer', 'exists:sport_sections,id'],
        ]);

        // UI sendet "0" für "keine" -> als NULL speichern
        if (($validated['default_organiser_id'] ?? null) === 0 || ($validated['default_organiser_id'] ?? null) === '0') {
            $validated['default_organiser_id'] = null;
        }
        if (($validated['default_sportSection_id'] ?? null) === 0 || ($validated['default_sportSection_id'] ?? null) === '0') {
            $validated['default_sportSection_id'] = null;
        }

        // SportSection muss zum Organiser passen
        if (!empty($validated['default_organiser_id']) && !empty($validated['default_sportSection_id'])) {
            $exists = DB::table('organiser_sport_section')
                ->where('organiser_id', (int)$validated['default_organiser_id'])
                ->where('sport_section_id', (int)$validated['default_sportSection_id'])
                ->exists();

            if (!$exists) {
                return back()
                    ->withErrors(['default_sportSection_id' => 'Die ausgewählte Abteilung gehört nicht zur ausgewählten Veranstaltung (Organiser).'])
                    ->withInput();
            }
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

        $selectedOrganiserId = (int)old('default_organiser_id', $trainertyp->default_organiser_id ?? 0);

        $sportSections = collect();
        if ($selectedOrganiserId > 0) {
            $sportSectionIds = DB::table('organiser_sport_section')
                ->where('organiser_id', $selectedOrganiserId)
                ->pluck('sport_section_id')
                ->map(fn ($v) => (int)$v)
                ->all();

            if (!empty($sportSectionIds)) {
                $sportSections = SportSection::query()
                    ->whereIn('id', $sportSectionIds)
                    ->whereNull('deleted_at')
                    ->orderBy('abteilung')
                    ->get();
            }
        }

        return view('admin.trainertyp.edit', [
            'type' => $trainertyp,
            'organisers' => $organisers,
            'sportSections' => $sportSections,
        ]);
    }

    public function update(Request $request, Trainertyp $trainertyp): RedirectResponse
    {
        $validated = $request->validate([
            'trainerfunktion' => ['required', 'string', 'max:255'],
            'status' => ['required', 'integer', 'in:0,1'],
            'default_sichtbar' => ['required', 'integer', 'in:0,1'],
            'default_organiser_id' => ['nullable', 'integer', 'exists:organisers,id'],
            'default_sportSection_id' => ['nullable', 'integer', 'exists:sport_sections,id'],
        ]);

        // UI sendet "0" für "keine" -> als NULL speichern
        if (($validated['default_organiser_id'] ?? null) === 0 || ($validated['default_organiser_id'] ?? null) === '0') {
            $validated['default_organiser_id'] = null;
        }
        if (($validated['default_sportSection_id'] ?? null) === 0 || ($validated['default_sportSection_id'] ?? null) === '0') {
            $validated['default_sportSection_id'] = null;
        }

        if (!empty($validated['default_organiser_id']) && !empty($validated['default_sportSection_id'])) {
            $exists = DB::table('organiser_sport_section')
                ->where('organiser_id', (int)$validated['default_organiser_id'])
                ->where('sport_section_id', (int)$validated['default_sportSection_id'])
                ->exists();

            if (!$exists) {
                return back()
                    ->withErrors(['default_sportSection_id' => 'Die ausgewählte Abteilung gehört nicht zur ausgewählten Veranstaltung (Organiser).'])
                    ->withInput();
            }
        }

        $trainertyp->fill($validated);
        $trainertyp->updated_at = Carbon::now();
        $trainertyp->save();

        // Werte auf bestehende Trainer-Zuordnungen übertragen (rückwirkend)
        // Regel (neu): Wenn default_-Einstellungen geändert werden, sollen ALLE vorhandenen Trainer-Zuordnungen
        // dieses Typs die neuen Einstellungen erhalten (auch deaktivierte/soft-gelöschte).
        // Hinweis: status/sichtbar auf dem Trainertable werden NICHT automatisch überschrieben.
        $updates = [
            'sportSection_id' => $validated['default_sportSection_id'] !== null ? (int)$validated['default_sportSection_id'] : null,
            'organiser_id' => $validated['default_organiser_id'] !== null ? (int)$validated['default_organiser_id'] : null,
            'bearbeiter_id' => Auth::id(),
            'updated_at' => Carbon::now(),
        ];

        DB::table('trainertables')
            ->where('trainertyp_id', $trainertyp->id)
            ->update($updates);

        // Optionaler Konsistenz-Check: wenn beides gesetzt ist, muss die Abteilung zum Organiser passen.
        // Diese Prüfung ist bereits bei der Validierung oben erfolgt.

        return back()->with('success', 'Trainertyp wurde gespeichert.');
    }

    public function destroy(Trainertyp $trainertyp): RedirectResponse
    {
        // Beim Deaktivieren soll status=0 gesetzt werden (zusätzlich zum SoftDelete)
        $trainertyp->status = 0;
        $trainertyp->updated_at = Carbon::now();
        $trainertyp->save();

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

