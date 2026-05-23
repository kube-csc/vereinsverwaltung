<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\RaceType;
use App\Models\RaceTypeTemplate;
use App\Models\RegattaTeam;
use App\Models\Tabledata;
use App\Models\Tabele;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegattaTeamManagerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Übersicht zur Verwaltung von Mannschaften (Gruppierung über teamlink) inkl. Vorschlagsliste.
     */
    public function index(Request $request)
    {
        $regattaId = session()->get('regattaSelectId');

        if (!$regattaId) {
            return redirect('/Regattamenu')->with('success', 'Bitte zuerst eine Regatta auswählen.');
        }

        $query = trim((string)$request->get('q', ''));
        $raceTypeId = $request->get('race_type_id');
        $teamlinkFilter = $request->get('teamlink_filter');

        // Basis: alle Teams der aktuellen Regatta
        $regattaTeamsQuery = RegattaTeam::query()
            ->with(['teamWertungsGruppe.raceTypeTemplate', 'regatta']);

        if ($teamlinkFilter === 'all_db_0') {
            // Regatta-übergreifend: nur Teams mit teamlink 0 oder NULL
            $regattaTeamsQuery->where(function($q) {
                $q->where('teamlink', 0)->orWhereNull('teamlink');
            });
        } elseif ($teamlinkFilter === 'once') {
            // Nur Teams, deren teamlink genau einmal vorkommt (ID > 0)
            $regattaTeamsQuery->where('regatta_id', $regattaId)
                ->where('teamlink', '>', 0)
                ->whereIn('teamlink', function($q) {
                    $q->select('teamlink')
                        ->from('regatta_teams')
                        ->where('teamlink', '>', 0)
                        ->groupBy('teamlink')
                        ->havingRaw('COUNT(*) = 1');
                });
        } else {
            // Standard: nur Teams der aktuellen Regatta
            $regattaTeamsQuery->where('regatta_id', $regattaId);

            if ($teamlinkFilter === '0') {
                $regattaTeamsQuery->where(function($q) {
                    $q->where('teamlink', 0)->orWhereNull('teamlink');
                });
            }
        }

        if ($query !== '') {
            $regattaTeamsQuery->where('teamname', 'like', '%' . $query . '%');
        }

        if ($raceTypeId) {
            $regattaTeamsQuery->where('gruppe_id', $raceTypeId);
        }

        $regattaTeams = $regattaTeamsQuery
            ->orderBy('teamlink')
            ->orderBy('teamname')
            ->orderBy('datum')
            ->get();

        // NEU: Für jedes Team mit teamlink > 0 andere Regatten laden, in denen sie gestartet sind
        foreach ($regattaTeams as $team) {
            $team->andereRegatten = collect();

            if ($team->teamlink > 0) {
                // Finde andere Teams mit gleichem teamlink, aber anderer Regatta
                $team->andereRegatten = RegattaTeam::query()
                    ->where('teamlink', $team->teamlink)
                    ->where('regatta_id', '!=', $regattaId)
                    ->with(['regatta', 'teamWertungsGruppe.raceTypeTemplate'])
                    ->get()
                    ->sortBy(function($item) {
                        return optional($item->regatta)->datumvon;
                    });
            }
        }

        // Alle verfügbaren Bootsklassen für das Dropdown laden (direkt aus race_types der Regatta)
        $raceTypes = RaceType::where('regatta_id', $regattaId)
            ->orderBy('typ')
            ->get();

        // Final-Teams der letzten Regatta ermitteln
        $finalTeamlinks = $this->getFinalTeamlinks($regattaId);

        return view('regattaManagement.regattaTeamManager.index', [
            'regattaId' => $regattaId,
            'regattaTeams' => $regattaTeams,
            'query' => $query,
            'raceTypeId' => $raceTypeId,
            'teamlinkFilter' => $teamlinkFilter,
            'raceTypes' => $raceTypes,
            'finalTeamlinks' => $finalTeamlinks,
        ]);
    }

    /**
     * Formular für den Textdatei-Import von Teams.
     */
    public function import()
    {
        $regattaId = session()->get('regattaSelectId');

        if (!$regattaId) {
            return redirect('/Regattamenu')->with('success', 'Bitte zuerst eine Regatta auswählen.');
        }

        $raceTypes = RaceType::where('regatta_id', $regattaId)
            ->orderBy('typ')
            ->get();

        $currentEvent = Event::find($regattaId);

        return view('regattaManagement.regattaTeamManager.import', [
            'regattaId' => $regattaId,
            'raceTypes' => $raceTypes,
            'currentEvent' => $currentEvent,
        ]);
    }

    /**
     * Importiert Teams aus einer Textdatei in die aktuelle Regatta.
     */
    public function importStore(Request $request)
    {
        $regattaId = session()->get('regattaSelectId');

        if (!$regattaId) {
            return redirect('/Regattamenu')->with('success', 'Bitte zuerst eine Regatta auswählen.');
        }

        $raceTypes = RaceType::where('regatta_id', $regattaId)
            ->orderBy('typ')
            ->get();

        if ($raceTypes->isEmpty()) {
            return redirect()
                ->route('regattaTeamManager.import')
                ->withErrors(['default_race_type_id' => 'Für diese Regatta existieren noch keine race_types. Bitte zuerst mindestens eine Zuordnung anlegen.'])
                ->withInput();
        }

        $validated = $request->validate([
            'team_names' => 'required|string',
            'default_race_type_id' => 'required|integer',
        ]);

        $defaultRaceType = $raceTypes->firstWhere('id', (int) $validated['default_race_type_id']);
        if (!$defaultRaceType) {
            return back()
                ->withErrors(['default_race_type_id' => 'Die gewählte race_type gehört nicht zur aktuellen Regatta.'])
                ->withInput();
        }

        $currentEvent = Event::find($regattaId);

        $content = $validated['team_names'];
        $lines = preg_split('/\R/u', (string) $content) ?: [];

        $report = [
            'created' => 0,
            'updated' => 0,
            'skipped' => 0,
            'warnings' => [],
        ];

        $processedTeamNames = [];

        foreach ($lines as $line) {
            $teamName = $this->extractTeamNameFromLine($line);

            if ($teamName === '') {
                continue;
            }

            $normalizedTeamName = $this->normalizeTeamName($teamName);
            $lookupKey = mb_strtolower($normalizedTeamName);

            if (in_array($lookupKey, $processedTeamNames, true)) {
                $report['skipped']++;
                $report['warnings'][] = 'Doppelte Zeile im Import wurde übersprungen: ' . $normalizedTeamName;
                continue;
            }

            $processedTeamNames[] = $lookupKey;

            $currentTeam = RegattaTeam::where('regatta_id', $regattaId)
                ->whereRaw('LOWER(TRIM(teamname)) = ?', [$lookupKey])
                ->first();

            $targetTemplateId = $defaultRaceType->race_type_template_id;
            if ($currentTeam && $currentTeam->gruppe_id) {
                $currentRaceType = RaceType::find($currentTeam->gruppe_id);
                if ($currentRaceType && $currentRaceType->race_type_template_id) {
                    $targetTemplateId = $currentRaceType->race_type_template_id;
                }
            }

            $sourceTeam = $this->findPreviousTeamRegistration($normalizedTeamName, $currentEvent, $targetTemplateId);

            $mappedRaceTypeId = $this->resolveRaceTypeIdForImport(
                $defaultRaceType,
                $sourceTeam,
                $regattaId
            );

            if (!$currentTeam) {
                $currentTeam = new RegattaTeam();
                $currentTeam->regatta_id = $regattaId;
                $currentTeam->datum = now();
                $currentTeam->status = 'Neuanmeldung';
                $currentTeam->training = 0;
                $currentTeam->passwort = ' ';
                $currentTeam->teamlink = 0;
                $currentTeam->mailen = ' ';
            }

            $baseValues = $this->buildImportValues($normalizedTeamName, $sourceTeam, $mappedRaceTypeId);
            $isNewTeam = !$currentTeam->exists;

            foreach ($baseValues as $field => $value) {
                if ($isNewTeam || $this->isEmptyImportValue($currentTeam->{$field} ?? null)) {
                    $currentTeam->{$field} = $value;
                }
            }

            // Pflichtfelder, die nicht leer sein dürfen, werden hier vorsorglich abgesichert.
            $currentTeam->teamname = $currentTeam->teamname ?: $normalizedTeamName;
            $currentTeam->gruppe_id = $currentTeam->gruppe_id ?: $mappedRaceTypeId;
            $currentTeam->status = $currentTeam->status ?: 'Neuanmeldung';
            $currentTeam->training = (int) ($currentTeam->training ?? 0);
            $currentTeam->teamlink = (int) ($currentTeam->teamlink ?? 0);
            $currentTeam->werbung = $currentTeam->werbung !== null && $currentTeam->werbung !== '' ? (string) $currentTeam->werbung : '0';
            $currentTeam->passwort = $currentTeam->passwort ?: ' ';
            $currentTeam->mailen = $currentTeam->mailen ?: ' ';

            if (!$currentTeam->email) {
                $currentTeam->email = 'import@invalid.local';
            }

            $currentTeam->save();

            if ($sourceTeam) {
                $this->syncTeamlinkOnImport($currentTeam, $sourceTeam);
            }

            if ($isNewTeam) {
                $report['created']++;
            } else {
                $report['updated']++;
            }
        }

        $successMessage = sprintf(
            'Import abgeschlossen: %d Teams neu angelegt, %d Teams ergänzt/aktualisiert, %d doppelte Zeilen übersprungen.',
            $report['created'],
            $report['updated'],
            $report['skipped']
        );

        return redirect()
            ->route('regattaTeamManager.index')
            ->with('success', $successMessage)
            ->with('importWarnings', $report['warnings']);
    }

    /**
     * Bearbeitungsmaske für den teamlink eines Teams.
     */
    public function edit(Request $request, $id)
    {
        $regattaId = session()->get('regattaSelectId');
        $team = RegattaTeam::with(['teamWertungsGruppe.raceTypeTemplate', 'regatta'])->findOrFail($id);

        if ($request->has('assign_new_teamlink')) {
            $existingTeamlinks = RegattaTeam::where('teamlink', '>', 0)
                ->distinct()
                ->orderBy('teamlink')
                ->pluck('teamlink')
                ->toArray();

            $nextFreeTeamlink = 1;
            foreach ($existingTeamlinks as $link) {
                if ($link == $nextFreeTeamlink) {
                    $nextFreeTeamlink++;
                } elseif ($link > $nextFreeTeamlink) {
                    break;
                }
            }
            $team->teamlink = $nextFreeTeamlink;
            $team->save();

            return redirect()->route('regattaTeamManager.edit', $id)->with('status', 'Neuer Teamlink wurde vergeben.');
        }

        $q_team = $request->get('q_team');
        $q_plz = $request->get('q_plz');
        $q_bootstyp = $request->get('q_bootstyp');

        // Die Vorschläge werden berechnet.
        // Teams mit dem gleichen teamlink (falls > 0) werden separat gelistet.
        $teamlink = $team->teamlink;
        $linkedTeams = collect();

        if ($teamlink > 0) {
            $linkedTeams = RegattaTeam::query()
                ->where('teamlink', $teamlink)
                ->where('regatta_teams.id', '!=', $team->id)
                ->with(['regatta', 'teamWertungsGruppe.raceTypeTemplate'])
                ->get();
        }

        $suggestionsQuery = RegattaTeam::query()
            ->where('regatta_teams.id', '!=', $team->id)
            ->with(['regatta', 'teamWertungsGruppe.raceTypeTemplate']);

        // Teams ausschließen, die bereits denselben teamlink haben
        if ($teamlink > 0) {
            $suggestionsQuery->where('teamlink', '!=', $teamlink);
        }

        $templateId = optional($team->teamWertungsGruppe)->race_type_template_id;

        // Wenn Filter gesetzt sind, diese bevorzugen
        if ($q_team || $q_plz || $q_bootstyp) {
            if ($q_team) {
                $suggestionsQuery->where('teamname', 'like', '%' . $q_team . '%');
            }
            if ($q_plz) {
                $suggestionsQuery->where('plz', $q_plz);
            }
            if ($q_bootstyp) {
                $suggestionsQuery->whereHas('teamWertungsGruppe', function($q) use ($q_bootstyp) {
                    $q->where('race_type_template_id', $q_bootstyp);
                });
            }
        } else {
            // Standard-Autovorschlag (Ähnlichkeit)
            if ($templateId) {
                $suggestionsQuery->whereHas('teamWertungsGruppe', function($q) use ($templateId) {
                    $q->where('race_type_template_id', $templateId);
                })
                ->where(function($q) use ($team) {
                    if ($team->teamname) {
                        $q->where('teamname', 'like', '%' . $team->teamname . '%');
                    }
                    if ($team->plz) {
                        $q->orWhere('plz', $team->plz);
                    }
                    if ($team->email) {
                        $q->orWhere('email', $team->email);
                    }
                });
            } else {
                // Wenn kein TemplateId, dann keine automatischen Vorschläge ohne Filter
                $suggestionsQuery->whereRaw('1=0');
            }
        }

        $suggestions = $suggestionsQuery->get();

        // Sortierung: Gleicher Bootstyp zuerst, dann nach Namensähnlichkeit (simuliert durch einfache Sortierung)
        $suggestions = $suggestions->sortBy([
            function ($a, $b) use ($templateId) {
                $aIsSame = optional($a->teamWertungsGruppe)->race_type_template_id == $templateId;
                $bIsSame = optional($b->teamWertungsGruppe)->race_type_template_id == $templateId;
                if ($aIsSame && !$bIsSame) return -1;
                if (!$aIsSame && $bIsSame) return 1;
                return 0;
            },
            ['teamname', 'asc'],
        ]);

        // JETZT extrahieren wir die PLZ und Bootstypen NUR aus den tatsächlich gefundenen Vorschlägen.
        $plz_options = $suggestions->pluck('plz')->filter()->unique()->values()->sort();

        // Final-Teams der letzten Regatta ermitteln
        $finalTeamlinks = $this->getFinalTeamlinks($regattaId);

        $bootstypen_ids = $suggestions->map(function($s) {
            return optional($s->teamWertungsGruppe)->race_type_template_id;
        })->filter()->unique();

        $bootstypen = RaceTypeTemplate::whereIn('id', $bootstypen_ids)
            ->orderBy('typ')
            ->get();

        // Nächste freie Teamlink-ID ermitteln (Kleinstmögliche freie ID suchen, um Lücken zu füllen)
        $existingTeamlinks = RegattaTeam::where('teamlink', '>', 0)
            ->distinct()
            ->orderBy('teamlink')
            ->pluck('teamlink')
            ->toArray();

        $nextFreeTeamlink = 1;
        foreach ($existingTeamlinks as $link) {
            if ($link == $nextFreeTeamlink) {
                $nextFreeTeamlink++;
            } elseif ($link > $nextFreeTeamlink) {
                break;
            }
        }

        return view('regattaManagement.regattaTeamManager.edit', [
            'team' => $team,
            'suggestions' => $suggestions,
            'linkedTeams' => $linkedTeams,
            'regattaId' => $regattaId,
            'bootstypen' => $bootstypen,
            'plz_options' => $plz_options,
            'q_team' => $q_team,
            'q_plz' => $q_plz,
            'q_bootstyp' => $q_bootstyp,
            'nextFreeTeamlink' => $nextFreeTeamlink,
            'finalTeamlinks' => $finalTeamlinks,
        ]);
    }

    /**
     * Update des teamlink.
     */
    public function update(Request $request, $id)
    {
        $team = RegattaTeam::findOrFail($id);
        $teamlink = $request->get('teamlink');

        $team->teamlink = $teamlink;
        $team->save();

        return redirect()->route('regattaTeamManager.index')->with('success', 'Teamlink wurde aktualisiert.');
    }

    /**
     * Synchronisiert den Teamlink zwischen zwei Teams.
     */
    public function sync(Request $request, $id)
    {
        $team = RegattaTeam::findOrFail($id);
        $otherTeam = RegattaTeam::findOrFail($request->get('other_id'));
        $direction = $request->get('direction');

        if ($direction === 'take') {
            // Aktuelles Team übernimmt den Teamlink vom Vorschlag
            $team->teamlink = $otherTeam->teamlink;
            $team->save();
            return redirect()->route('regattaTeamManager.edit', $team->id)->with('success', 'Teamlink vom Vorschlag übernommen.');
        } elseif ($direction === 'merge_new') {
            // Beide Teams haben teamlink=0 -> neue ID finden und beiden zuweisen
            $existingTeamlinks = RegattaTeam::where('teamlink', '>', 0)
                ->distinct()
                ->orderBy('teamlink')
                ->pluck('teamlink')
                ->toArray();

            $nextFree = 1;
            foreach ($existingTeamlinks as $link) {
                if ($link == $nextFree) {
                    $nextFree++;
                } elseif ($link > $nextFree) {
                    break;
                }
            }

            $team->teamlink = $nextFree;
            $team->save();

            $otherTeam->teamlink = $nextFree;
            $otherTeam->save();

            return redirect()->route('regattaTeamManager.edit', $team->id)->with('success', "Beide Teams wurden mit dem neuen Teamlink #{$nextFree} verknüpft.");
        } else {
            // Vorschlag übernimmt den Teamlink vom aktuellen Team
            $otherTeam->teamlink = $team->teamlink;
            $otherTeam->save();
            return redirect()->route('regattaTeamManager.edit', $otherTeam->id)->with('success', 'Vorschlag wurde dem Teamlink zugeordnet und wird nun bearbeitet.');
        }
    }

    private function extractTeamNameFromLine($line): string
    {
        $line = $this->normalizeTeamName((string) $line);

        $line = preg_replace('/^\s*[-*•]\s*/u', '', $line) ?? $line;
        $line = preg_replace('/^\s*\d+\s*[.) :]\s*/u', '', $line) ?? $line;
        $line = trim($line, " \t\n\r\0\x0B\"'");

        if ($line === '' || str_starts_with($line, '#')) {
            return '';
        }

        $parts = preg_split('/[;\t|,]/u', $line);
        $teamName = trim((string) ($parts[0] ?? ''));

        return $this->normalizeTeamName($teamName);
    }

    private function normalizeTeamName(string $teamName): string
    {
        $teamName = trim($teamName);
        $teamName = preg_replace('/\s+/u', ' ', $teamName) ?? $teamName;

        return trim($teamName);
    }

    private function isEmptyImportValue($value): bool
    {
        if ($value === null) {
            return true;
        }

        if (is_string($value)) {
            return trim($value) === '';
        }

        return false;
    }

    /**
     * Sucht die letzte gültige Meldung eines Teams vor dem aktuellen Event.
     *
     * @return \App\Models\RegattaTeam|null
     */
    private function findPreviousTeamRegistration(string $teamName, ?Event $currentEvent, ?int $targetTemplateId = null): ?RegattaTeam
    {
        if (!$currentEvent) {
            return null;
        }

        $query = RegattaTeam::query()
            ->select('regatta_teams.*')
            ->join('events', 'events.id', '=', 'regatta_teams.regatta_id')
            ->join('race_types', 'race_types.id', '=', 'regatta_teams.gruppe_id')
            ->with(['teamWertungsGruppe.raceTypeTemplate', 'regatta'])
            ->whereRaw('LOWER(TRIM(regatta_teams.teamname)) = ?', [mb_strtolower($this->normalizeTeamName($teamName))])
            ->where('events.regatta', 1)
            ->where('regatta_teams.status', '!=', 'Gelöscht');

        if ($targetTemplateId) {
            $query->where('race_types.race_type_template_id', $targetTemplateId);
        }

        if ($currentEvent->datumvon) {
            $query->where('events.datumvon', '<', $currentEvent->datumvon);
        } else {
            $query->where('events.id', '<', $currentEvent->id);
        }

        /** @var \App\Models\RegattaTeam|null $previousTeam */
        $previousTeam = $query->orderByDesc('events.datumvon')->orderByDesc('events.id')->first();

        return $previousTeam;
    }

    /**
     * Verknüpft ein importiertes Team mit der letzten passenden Meldung über teamlink.
     *
     * Regel:
     * - Existiert bereits ein teamlink (>0), wird dieser für beide verwendet.
     * - Haben beide teamlink=0, wird eine neue freie teamlink-ID erzeugt und beiden zugewiesen.
     */
    private function syncTeamlinkOnImport(RegattaTeam $currentTeam, RegattaTeam $sourceTeam): void
    {
        $currentTemplateId = optional($currentTeam->teamWertungsGruppe)->race_type_template_id;
        $sourceTemplateId = optional($sourceTeam->teamWertungsGruppe)->race_type_template_id;

        if (!$currentTemplateId || !$sourceTemplateId || (int) $currentTemplateId !== (int) $sourceTemplateId) {
            return;
        }

        $currentLink = (int) ($currentTeam->teamlink ?? 0);
        $sourceLink = (int) ($sourceTeam->teamlink ?? 0);

        $linkToUse = 0;
        if ($currentLink > 0) {
            $linkToUse = $currentLink;
        } elseif ($sourceLink > 0) {
            $linkToUse = $sourceLink;
        } else {
            $linkToUse = $this->getNextFreeTeamlinkId();
        }

        if ($currentLink !== $linkToUse) {
            $currentTeam->teamlink = $linkToUse;
            $currentTeam->save();
        }

        if ($sourceLink !== $linkToUse) {
            $sourceTeam->teamlink = $linkToUse;
            $sourceTeam->save();
        }
    }

    private function getNextFreeTeamlinkId(): int
    {
        $existingTeamlinks = RegattaTeam::where('teamlink', '>', 0)
            ->distinct()
            ->orderBy('teamlink')
            ->pluck('teamlink')
            ->toArray();

        $nextFree = 1;
        foreach ($existingTeamlinks as $link) {
            if ((int) $link === $nextFree) {
                $nextFree++;
            } elseif ((int) $link > $nextFree) {
                break;
            }
        }

        return $nextFree;
    }

    private function resolveRaceTypeIdForImport(RaceType $defaultRaceType, ?RegattaTeam $sourceTeam, int $currentRegattaId): int
    {
        if ($sourceTeam && $sourceTeam->gruppe_id) {
            $sourceRaceType = RaceType::find($sourceTeam->gruppe_id);

            if ($sourceRaceType) {
                $mappedByTemplate = RaceType::where('regatta_id', $currentRegattaId)
                    ->where('race_type_template_id', $sourceRaceType->race_type_template_id)
                    ->first();

                if ($mappedByTemplate) {
                    return (int) $mappedByTemplate->id;
                }

                $mappedByName = RaceType::where('regatta_id', $currentRegattaId)
                    ->where('typ', $sourceRaceType->typ)
                    ->first();

                if ($mappedByName) {
                    return (int) $mappedByName->id;
                }
            }
        }

        return (int) $defaultRaceType->id;
    }

    private function buildImportValues(string $teamName, ?RegattaTeam $sourceTeam, int $raceTypeId): array
    {
        $fallbackText = ' ';

        return [
            'teamname' => $teamName,
            'verein' => $this->valueOrFallback($sourceTeam->verein ?? null, $fallbackText),
            'teamcaptain' => $this->valueOrFallback($sourceTeam->teamcaptain ?? null, $fallbackText),
            'strasse' => $this->valueOrFallback($sourceTeam->strasse ?? null, $fallbackText),
            'plz' => $this->valueOrFallback($sourceTeam->plz ?? null, $fallbackText),
            'ort' => $this->valueOrFallback($sourceTeam->ort ?? null, $fallbackText),
            'telefon' => $this->valueOrFallback($sourceTeam->telefon ?? null, $fallbackText),
            'email' => $this->valueOrFallback($sourceTeam->email ?? null, 'import@invalid.local'),
            'homepage' => $this->valueOrFallback($sourceTeam->homepage ?? null, $fallbackText),
            'beschreibung' => $this->valueOrFallback($sourceTeam->beschreibung ?? null, $fallbackText),
            'kommentar' => $this->valueOrFallback($sourceTeam->kommentar ?? null, $fallbackText),
            'gruppe_id' => $raceTypeId,
            'status' => $this->valueOrFallback($sourceTeam->status ?? null, 'Neuanmeldung'),
            'passwort' => $this->valueOrFallback($sourceTeam->passwort ?? null, $fallbackText),
            'mailen' => $this->valueOrFallback($sourceTeam->mailen ?? null, $fallbackText),
            'werbung' => $this->valueOrFallback($sourceTeam->werbung ?? null, '0'),
            'teamlink' => $sourceTeam && (int) $sourceTeam->teamlink > 0 ? (int) $sourceTeam->teamlink : 0,
            'training' => $sourceTeam ? (int) ($sourceTeam->training ?? 0) : 0,
            'datum' => now(),
        ];
    }

    private function valueOrFallback($value, string $fallback): string
    {
        if ($value === null) {
            return $fallback;
        }

        $value = trim((string) $value);

        return $value === '' ? $fallback : $value;
    }

    /**
     * Ermittelt die Teamlink-IDs und Platzierungen der Teams, die bei der letzten Regatta der gleichen Gruppe in einem Finale waren.
     * Die Platzierung wird basierend auf Punkten oder Zeit berechnet.
     */
    private function getFinalTeamlinks($currentRegattaId)
    {
        $currentEvent = Event::find($currentRegattaId);
        if (!$currentEvent || !$currentEvent->eventGroup_id) {
            return [];
        }

        // Finde das letzte Event derselben Gruppe (zeitlich vor dem aktuellen)
        $lastEvent = Event::where('eventGroup_id', $currentEvent->eventGroup_id)
            ->where('id', '!=', $currentRegattaId)
            ->where('datumvon', '<', $currentEvent->datumvon)
            ->orderBy('datumvon', 'desc')
            ->first();

        if (!$lastEvent) {
            return [];
        }

        // Finde alle Final-Tabellen dieses Events
        $finalTables = Tabele::where('event_id', $lastEvent->id)
            ->where('finale', '>', 0)
            ->get();

        $results = [];

        foreach ($finalTables as $table) {
            $data = Tabledata::join('regatta_teams', 'tabledatas.mannschaft_id', '=', 'regatta_teams.id')
                ->where('tabledatas.tabele_id', $table->id)
                ->where('regatta_teams.teamlink', '>', 0)
                ->select('tabledatas.*', 'regatta_teams.teamlink')
                ->get();

            if ($table->wertungsart == 2) {
                // Zeit-Wertung: Kleinste Zeit gewinnt
                $sorted = $data->sort(function ($a, $b) {
                    if ($a->zeit === $b->zeit) {
                        return $a->hundert <=> $b->hundert;
                    }
                    return $a->zeit <=> $b->zeit;
                });
            } else {
                // Punkt-Wertung (Standard): Höchste Punkte gewinnen
                $sorted = $data->sortByDesc('punkte');
            }

            $rank = 1;
            foreach ($sorted as $row) {
                // Falls ein Teamlink in mehreren Finalen auftaucht (unwahrscheinlich), gewinnt die bessere Platzierung
                if (!isset($results[$row->teamlink]) || $rank < $results[$row->teamlink]['platz']) {
                    $results[$row->teamlink] = [
                        'platz' => $rank,
                        'tabelle' => $table->ueberschrift
                    ];
                }
                $rank++;
            }
        }

        return $results;
    }
}

