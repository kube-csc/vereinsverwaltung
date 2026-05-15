<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Regattaverwaltung') }}: {{ Session::get('regattaSelectUeberschrift') }}
        </h2>
    </x-slot>

    <div class="max-w-6xl mx-auto py-8">
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold">Logik der Regatta-Rennplanerstellung</h1>
                    <p class="text-sm text-gray-600 mt-1">
                        Diese Seite zeigt die Logik-Spezifikation für die automatisierte Rennplanerstellung.
                    </p>
                </div>

                <div class="shrink-0">
                    <a href="/Regattamenu" class="bg-gray-200 hover:bg-gray-300 text-gray-900 font-semibold py-2 px-4 rounded">
                        Zurück zum Regattamenü
                    </a>
                </div>
            </div>

            <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Linke Seite: Daten-Übersicht -->
                <div class="space-y-4">
                    <h3 class="font-bold text-lg">Daten-Übersicht</h3>
                    <div class="bg-gray-50 rounded-lg p-4 border">
                        <p class="text-sm text-gray-700 mb-4">
                            Diese Daten dienen als Test- und Referenzdaten für die Entwicklung und Validierung der Planungs-Logik.
                        </p>

                        <div class="overflow-x-auto">
                            <table class="min-w-full text-xs">
                                <thead class="bg-gray-200">
                                    <tr>
                                        <th class="px-2 py-1 text-left">ID</th>
                                        <th class="px-2 py-1 text-left">Team</th>
                                        <th class="px-2 py-1 text-left">Klasse</th>
                                        <th class="px-2 py-1 text-left">PLZ / Ort</th>
                                        <th class="px-2 py-1 text-left">Verein</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @php
                                        // Gruppiere Teams nach den ODER-Kriterien (Verein, PLZ, Ort)
                                        // Wir erstellen "Blöcke", um zu visualisieren, welche Teams zusammengehören
                                        $orgBlocks = [];
                                        $processedTeamIds = [];
                                        foreach($teams as $team) {
                                            if (in_array($team->id, $processedTeamIds)) continue;

                                            $keys = array_filter([$team->verein, $team->plz, $team->ort]);
                                            $foundBlock = false;
                                            foreach($orgBlocks as &$block) {
                                                foreach($keys as $k) {
                                                    if(in_array($k, $block['criteria'])) {
                                                        $block['teams'][$team->id] = $team;
                                                        $block['criteria'] = array_unique(array_merge($block['criteria'], $keys));
                                                        $foundBlock = true;
                                                        $processedTeamIds[] = $team->id;
                                                        break 2;
                                                    }
                                                }
                                            }
                                            if(!$foundBlock) {
                                                $orgBlocks[] = [
                                                    'criteria' => $keys,
                                                    'teams' => [$team->id => $team]
                                                ];
                                                $processedTeamIds[] = $team->id;
                                            }
                                        }

                                        // Konsolidierung der Blöcke (falls durch ODER-Ketten Blöcke verschmelzen)
                                        $changed = true;
                                        while($changed) {
                                            $changed = false;
                                            for($i=0; $i < count($orgBlocks); $i++) {
                                                for($j=$i+1; $j < count($orgBlocks); $j++) {
                                                    $intersect = array_intersect($orgBlocks[$i]['criteria'], $orgBlocks[$j]['criteria']);
                                                    if(!empty($intersect)) {
                                                        $orgBlocks[$i]['criteria'] = array_unique(array_merge($orgBlocks[$i]['criteria'], $orgBlocks[$j]['criteria']));
                                                        // Teams mergen und dabei Eindeutigkeit über die ID (Key) bewahren
                                                        foreach($orgBlocks[$j]['teams'] as $id => $t) {
                                                            $orgBlocks[$i]['teams'][$id] = $t;
                                                        }
                                                        array_splice($orgBlocks, $j, 1);
                                                        $changed = true;
                                                        break 2;
                                                    }
                                                }
                                            }
                                        }
                                    @endphp
                                    @foreach($orgBlocks as $index => $block)
                                        @foreach($block['teams'] as $teamIndex => $team)
                                            <tr class="{{ $index % 2 == 0 ? 'bg-white' : 'bg-gray-50' }} hover:bg-blue-50 transition-colors">
                                                <td class="px-2 py-1 text-gray-400 font-mono">{{ $team->id }}</td>
                                                <td class="px-2 py-1 border-l-2 {{ $index % 2 == 0 ? 'border-blue-400' : 'border-indigo-400' }}">
                                                    <div class="flex items-center gap-1">
                                                        <span class="font-medium">{{ $team->teamname }}</span>
                                                        @if($team->teamlink > 0 && isset($finalTeamlinks[$team->teamlink]))
                                                            <span title="War bei der letzten Regatta in einem Finale ({{ $finalTeamlinks[$team->teamlink]['tabelle'] }}, Platz {{ $finalTeamlinks[$team->teamlink]['platz'] }})" class="cursor-help whitespace-nowrap">🏆 {{ $finalTeamlinks[$team->teamlink]['platz'] }}.</span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="px-2 py-1 text-gray-500">{{ optional($team->teamWertungsGruppe)->typ }}</td>
                                                <td class="px-2 py-1">
                                                    <div class="flex flex-wrap gap-1">
                                                        <span class="px-1 rounded {{ $team->plz ? 'bg-blue-100 text-blue-800' : 'text-gray-400' }}">{{ $team->plz ?: '-' }}</span>
                                                        <span class="px-1 rounded {{ $team->ort ? 'bg-indigo-100 text-indigo-800' : 'text-gray-400' }}">{{ $team->ort ?: '-' }}</span>
                                                    </div>
                                                </td>
                                                <td class="px-2 py-1">
                                                    <span class="px-1 rounded {{ $team->verein ? 'bg-purple-100 text-purple-800 font-medium' : 'text-gray-400' }}">
                                                        {{ $team->verein ?: '-' }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                        @if(!$loop->last)
                                            <tr class="h-1 bg-gray-200"><td colspan="4"></td></tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Rechte Seite: Generator -->
                <div>
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                        <h3 class="font-bold text-yellow-800 flex items-center">
                            <box-icon name='terminal' class="mr-2" color="#854d0e"></box-icon>
                            Rennplan generieren
                        </h3>
                        <form action="{{ route('regattaRaffle.generate') }}" method="POST" class="mt-4 space-y-4">
                            @csrf
                            <input type="hidden" name="mode" value="full">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Anzahl der Vorläufe</label>
                                <input type="number" name="heats_count" value="{{ Session::get('raffleParams.heats_count', 3) }}" min="1" max="10" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Startzeit</label>
                                    <input type="time" name="start_time" value="{{ Session::get('raffleParams.start_time', '10:00') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Intervall (Minuten)</label>
                                    <input type="number" name="interval" value="{{ Session::get('raffleParams.interval', 10) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Wertungsmodus</label>
                                <select name="wertungsart" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="1" {{ Session::get('raffleParams.wertungsart') == 1 ? 'selected' : '' }}>Punktwertung</option>
                                    <option value="2" {{ Session::get('raffleParams.wertungsart') == 2 ? 'selected' : '' }}>Zeitwertung</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Mindestpause (Min.)</label>
                                <input type="number" name="min_pause" value="{{ Session::get('raffleParams.min_pause', 20) }}" min="0" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            </div>
                            <div class="border-t pt-4 mt-4">
                                <label class="block text-sm font-bold text-blue-800">Final-Einstellungen</label>
                                <div class="grid grid-cols-2 gap-4 mt-2">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Anzahl pro Gruppe</label>
                                        <input type="number" name="finals_count" value="{{ Session::get('raffleParams.finals_count', 1) }}" min="0" max="{{ $maxFinalsTotal ?? 10 }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Startzeit Finals</label>
                                        <input type="time" name="finals_start_time" value="{{ Session::get('raffleParams.finals_start_time', '14:00') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <label class="block text-sm font-medium text-gray-700">Pause nach Vorläufen (Min.)</label>
                                    <input type="number" name="pause_after_heats" value="{{ Session::get('raffleParams.pause_after_heats', 30) }}" min="0" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                </div>
                                <div class="mt-2">
                                    <label class="block text-sm font-medium text-gray-700">Veröffentlichung Finale (Uhrzeit)</label>
                                    <input type="time" name="finale_publish_time" value="{{ Session::get('raffleParams.finale_publish_time', '19:00') }}" class="mt-1 block w-full bg-gray-100 border-gray-300 rounded-md shadow-sm" readonly>
                                    <p class="text-xs text-gray-500 mt-1 italic">Wird automatisch auf 1 Stunde nach der Siegerehrung gesetzt.</p>
                                </div>
                                <div class="mt-2">
                                    <label class="block text-sm font-medium text-gray-700">Mindestzeit bis zur Siegerehrung (Minuten)</label>
                                    <input type="number" name="min_time_before_ceremony" value="{{ Session::get('raffleParams.min_time_before_ceremony', 30) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    <p class="text-xs text-gray-500 mt-1 italic">Mindestpause zwischen dem letzten Rennen und der Siegerehrung.</p>
                                </div>
                                <div class="mt-2">
                                    <label class="block text-sm font-medium text-gray-700">Siegerehrung (Uhrzeit)</label>
                                    <input type="time" name="award_ceremony_time" value="{{ Session::get('raffleParams.award_ceremony_time', '18:00') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    <p class="text-xs text-gray-500 mt-1 italic">Voraussichtlicher Zeitpunkt der Siegerehrung.</p>
                                </div>
                            </div>

                            <div class="flex gap-2">
                                <button type="submit" class="flex-1 bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded shadow">
                                    Komplett neu verlosen
                                </button>
                                @if(isset($previewData) && count($previewData) > 0)
                                    <button type="submit" formaction="{{ route('regattaRaffle.recalculate') }}" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow">
                                        Nur Zeiten aktualisieren
                                    </button>
                                @endif
                            </div>
                        </form>
                    </div>

                    @if(isset($previewData) && count($previewData) > 0)
                        <div class="bg-green-50 border-l-4 border-green-400 p-4 mt-6">
                            <h3 class="font-bold text-green-800 flex items-center">
                                <box-icon name='table' class="mr-2" color="#166534"></box-icon>
                                Vorschau des Rennplans
                            </h3>
                            <div class="mt-4 space-y-8">
                                @php
                                    $groupedPreview = collect($previewData)->groupBy('gruppe_name');
                                @endphp

                                @foreach($groupedPreview as $gruppeName => $rows)
                                    @if($gruppeName === 'Unbekannt' || empty($gruppeName))
                                        @php
                                            // Falls es nur die Siegerehrung in dieser "Gruppe" gibt, überspringen wir sie in der gruppierten Ansicht
                                            $hasRealRaces = $rows->contains(fn($r) => empty($r['is_award_ceremony']));
                                        @endphp
                                        @if(!$hasRealRaces)
                                            @continue
                                        @endif
                                    @endif
                                    @php
                                        // Anzahl der einzigartigen Teams in dieser Gruppe zählen (nur für Vorläufe, da Finals Platzhalter haben)
                                        $teamCountInGroup = $rows->where('is_final', false)->pluck('team_id')->unique()->count();
                                    @endphp
                                    <div class="border rounded-lg overflow-hidden">
                                        <div class="bg-green-100 px-4 py-2 font-bold text-green-800 border-b flex justify-between items-center">
                                            <span>Wertungsgruppe: {{ $gruppeName }}</span>
                                            @if($teamCountInGroup > 0)
                                                <span class="text-xs bg-green-200 text-green-900 px-2 py-0.5 rounded-full">
                                                    {{ $teamCountInGroup }} Teams gemeldet
                                                </span>
                                            @endif
                                        </div>
                                        <div class="overflow-x-auto">
                                            <table class="min-w-full text-xs">
                                                <thead class="bg-gray-50">
                                                    <tr>
                                                        <th class="px-2 py-1 text-left">Zeit</th>
                                                        <th class="px-2 py-1 text-left">Pause <span class="text-[9px] font-normal text-gray-400">(Min)</span></th>
                                                        <th class="px-2 py-1 text-left">Konflikte</th>
                                                        <th class="px-2 py-1 text-left">Vorlauf / Finale</th>
                                                        <th class="px-2 py-1 text-left">Rennen</th>
                                                        <th class="px-2 py-1 text-left">Bahn</th>
                                                        <th class="px-2 py-1 text-left">Team</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-green-200 bg-white">
                                                    @foreach($rows as $row)
                                                        @if(!empty($row['is_award_ceremony'])) @continue @endif
                                                         <tr>
                                                            <td class="px-2 py-1 font-semibold">{{ $row['time'] }}</td>
                                                            <td class="px-2 py-1 text-gray-500">
                                                                <div>{{ $row['pause'] ?? '-' }}</div>
                                                                @if(isset($row['org_pause']) && $row['org_pause'] !== '-')
                                                                    <div class="text-[10px] text-blue-500 font-medium cursor-help" title="Abstand zur letzten Aktivität derselben Organisation">
                                                                        {{ $row['org_pause'] }}
                                                                    </div>
                                                                @endif
                                                            </td>
                                                            <td class="px-2 py-1">
                                                                @php
                                                                    $conflicts = $row['conflicts'] ?? 0;
                                                                @endphp
                                                                @if($conflicts > 0)
                                                                    <span class="text-red-600 font-bold" title="Wiederholte Gegner">{{ $conflicts }}x</span>
                                                                @else
                                                                    <span class="text-gray-400">-</span>
                                                                @endif
                                                            </td>
                                                            <td class="px-2 py-1">
                                                                @if($row['is_final'])
                                                                    <span class="font-bold text-blue-600">{{ $row['final_type'] }}</span>
                                                                @else
                                                                    {{ $row['heat_index'] ?? 1 }}. VL
                                                                @endif
                                                            </td>
                                                            <td class="px-2 py-1">@if(!empty($row['is_award_ceremony'])) - @else Lauf {{ $row['race_number'] ?? '?' }} @endif</td>
                                                            <td class="px-2 py-1">@if(isset($row['lane'])) Bahn {{ $row['lane'] }} @else - @endif</td>
                                                            <td class="px-2 py-1">
                                                                <div class="flex flex-col gap-0.5">
                                                                    <div class="flex items-center gap-2">
                                                                        <span class="font-medium">{{ $row['team_name'] }}</span>
                                                                        @if($row['has_pokal'] ?? false)
                                                                            @php
                                                                                $pokalData = $row['last_final_platz'] ?? [];
                                                                                $platz = is_array($pokalData) ? ($pokalData['platz'] ?? '?') : $pokalData;
                                                                                $titel = is_array($pokalData) ? ($pokalData['tabelle'] ?? 'Finale') : 'Finale';
                                                                            @endphp
                                                                            <span title="War bei der letzten Regatta in einem Finale ({{ $titel }}, Platz {{ $platz }})" class="cursor-help whitespace-nowrap">🏆 {{ $platz }}.</span>
                                                                        @endif
                                                                    </div>
                                                                    @if(isset($row['org_pause']) && $row['org_pause'] !== '-')
                                                                        <div class="text-[9px] text-blue-400 italic leading-tight cursor-help" title="Abstand zur letzten Aktivität derselben Organisation)">
                                                                            ({{ $row['org_name'] ?? 'Verein/Ort' }}{{ !empty($row['org_team_name']) ? ', Team: '.$row['org_team_name'] : '' }})
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </td>
                                                        </tr>
                                                   @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <form action="{{ route('regattaRaffle.store') }}" method="POST" class="mt-4">
                                @csrf
                                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded" onclick="return confirm('Soll der Rennplan so gespeichert werden?')">
                                    Plan übernehmen & speichern
                                </button>
                            </form>
                        </div>

                        @if(isset($teamOpponents) && count($teamOpponents) > 0)
                            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mt-6">
                                <h3 class="font-bold text-blue-800 flex items-center mb-4">
                                    <box-icon name='time-five' class="mr-2" color="#1e40af"></box-icon>
                                    Gesamt-Zeitplan
                                </h3>
                                <div class="overflow-x-auto bg-white border rounded-lg">
                                    <table class="min-w-full text-xs">
                                        <thead class="bg-gray-100">
                                            <tr>
                                                <th class="px-2 py-1 text-left" style="width: 80px;">Zeit</th>
                                                <th class="px-2 py-1 text-center" style="width: 60px;">Sort.</th>
                                                <th class="px-2 py-1 text-left">Rennen</th>
                                                <th class="px-2 py-1 text-left">Gruppe</th>
                                                <th class="px-2 py-1 text-left">Typ</th>
                                                <th class="px-2 py-1 text-left">Bahn/Team</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                            @php
                                                $chronologicalRaces = collect($previewData)->groupBy(function($item) {
                                                    return $item['time'] . '_' . ($item['race_number'] ?? 'special');
                                                })->sortBy(function($item, $key) {
                                                    return $key;
                                                });
                                                $pauseShown = false;
                                                $maxHeatEnd = $maxHeatEndTime ?? null;
                                            @endphp

                                            @foreach($chronologicalRaces as $raceId => $lanes)
                                                @php
                                                    $firstLane = $lanes->first();
                                                    $raceTime = $firstLane['time'];
                                                    $raceNumber = $firstLane['race_number'] ?? null;
                                                @endphp

                                                @if(!$pauseShown && $maxHeatEnd && $raceTime > $maxHeatEnd && ($firstLane['is_final'] ?? false))
                                                    <tr class="bg-yellow-100">
                                                        <td colspan="6" class="px-4 py-2 text-center font-bold text-yellow-800">
                                                            --- PAUSENBLOCK NACH DEN VORLÄUFEN ---
                                                        </td>
                                                    </tr>
                                                    @php $pauseShown = true; @endphp
                                                @endif

                                                @if(isset($firstLane['is_award_ceremony']) && $firstLane['is_award_ceremony'])
                                                    <tr class="bg-purple-100">
                                                        <td class="px-2 py-4 font-bold text-purple-900">{{ $raceTime }}</td>
                                                        <td colspan="5" class="px-4 py-4 text-center font-bold text-purple-900 uppercase tracking-widest">
                                                            <box-icon name='trophy' class="inline-block mr-2" color="#581c87" size="xs"></box-icon>
                                                            Siegerehrung
                                                        </td>
                                                    </tr>
                                                    @continue
                                                @endif

                                                <tr class="{{ $firstLane['is_final'] ? 'bg-blue-50' : '' }}">
                                                    <td class="px-2 py-2 font-bold">{{ $raceTime }}</td>
                                                    <td class="px-2 py-2 text-center">
                                                        <div class="flex flex-col items-center gap-1">
                                                            @if(!$loop->first)
                                                                <form action="{{ route('regattaRaffle.move') }}" method="POST">
                                                                    @csrf
                                                                    <input type="hidden" name="race_number" value="{{ $raceNumber }}">
                                                                    <input type="hidden" name="direction" value="up">
                                                                    <button type="submit" class="text-blue-600 hover:text-blue-800" title="Nach oben verschieben">
                                                                        <box-icon name='chevron-up' size="xs"></box-icon>
                                                                    </button>
                                                                </form>
                                                            @endif
                                                            @if(!$loop->last)
                                                                <form action="{{ route('regattaRaffle.move') }}" method="POST">
                                                                    @csrf
                                                                    <input type="hidden" name="race_number" value="{{ $raceNumber }}">
                                                                    <input type="hidden" name="direction" value="down">
                                                                    <button type="submit" class="text-blue-600 hover:text-blue-800" title="Nach unten verschieben">
                                                                        <box-icon name='chevron-down' size="xs"></box-icon>
                                                                    </button>
                                                                </form>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td class="px-2 py-2">Lauf {{ $firstLane['race_number'] }}</td>
                                                    <td class="px-2 py-2">{{ $firstLane['gruppe_name'] }}</td>
                                                    <td class="px-2 py-2">
                                                        @if($firstLane['is_final'])
                                                            <span class="text-blue-600 font-bold">{{ $firstLane['final_type'] }}</span>
                                                        @else
                                                            {{ $firstLane['heat_index'] }}. VL
                                                        @endif
                                                    </td>
                                                    <td class="px-2 py-2">
                                                        <div class="grid grid-cols-1 gap-1">
                                                            @foreach($lanes->sortBy('lane') as $l)
                                                                <div class="flex flex-col gap-0.5 border-b border-gray-100 last:border-0 pb-0.5 mb-0.5 last:mb-0">
                                                                    <div class="flex items-center gap-2">
                                                                        <span class="text-gray-400">@if(isset($l['lane'])) B{{ $l['lane'] }}: @else - @endif</span>
                                                                        <span class="font-medium">{{ $l['team_name'] }}</span>
                                                                        @if($l['has_pokal'] ?? false)
                                                                            @php
                                                                                $pokalData = $l['last_final_platz'] ?? [];
                                                                                $platz = is_array($pokalData) ? ($pokalData['platz'] ?? '?') : $pokalData;
                                                                                $titel = is_array($pokalData) ? ($pokalData['tabelle'] ?? 'Finale') : 'Finale';
                                                                            @endphp
                                                                            <span title="War bei der letzten Regatta in einem finale ({{ $titel }}, Platz {{ $platz }})" class="cursor-help whitespace-nowrap">🏆 {{ $platz }}.</span>
                                                                        @endif
                                                                    </div>
                                                                    @if(isset($l['org_pause']) && $l['org_pause'] !== '-')
                                                                        <div class="text-[9px] text-blue-400 italic leading-tight ml-5 cursor-help" title="Abstand zur letzten Aktivität derselben Organisation">
                                                                            {{ $l['org_pause'] }} Min ({{ $l['org_name'] ?? 'Verein/Ort' }}{{ !empty($l['org_team_name']) ? ', Team: '.$l['org_team_name'] : '' }})
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mt-6">
                                <h3 class="font-bold text-blue-800 flex items-center mb-4">
                                    <box-icon name='group' class="mr-2" color="#1e40af"></box-icon>
                                    Gegner-Übersicht pro Team
                                </h3>
                                <div class="space-y-6">
                                    @foreach($teamOpponents as $gruppeName => $teams)
                                        <div class="border rounded-lg overflow-hidden">
                                            <div class="bg-blue-100 px-4 py-2 font-bold text-blue-800 border-b flex justify-between items-center">
                                                <span>Gruppe: {{ $gruppeName }}</span>
                                                <span class="text-xs bg-blue-200 text-blue-900 px-2 py-0.5 rounded-full">
                                                    {{ count($teams) }} Teams
                                                </span>
                                            </div>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4">
                                                @foreach($teams as $teamName => $opponents)
                                                    <div class="bg-white p-3 rounded shadow-sm border text-xs">
                                                        <div class="font-bold text-blue-900 border-b pb-1 mb-2">{{ $teamName }}</div>
                                                        <div class="text-gray-600">
                                                            @if(is_countable($opponents) && count($opponents) > 0)
                                                                {{ implode(', ', $opponents) }}
                                                            @else
                                                                <span class="italic">Keine Gegner (Einzellauf?)</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
