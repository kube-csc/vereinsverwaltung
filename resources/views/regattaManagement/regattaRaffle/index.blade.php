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
                <!-- Linke Seite: Daten-Übersicht & Organisations-Management -->
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="font-bold text-lg">Organisationen & Teams</h3>
                        <div class="flex gap-2">
                            <form action="{{ route('raffleOrganizations.reset') }}" method="POST" onsubmit="return confirm('Möchten Sie alle Organisationen und Team-Zuweisungen wirklich löschen und neu starten?')">
                                @csrf
                                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white text-xs font-semibold py-1 px-3 rounded flex items-center gap-1">
                                    <box-icon name='refresh' size="xs" color="white"></box-icon>
                                    Neu starten
                                </button>
                            </form>
                            @if($organizations->isEmpty())
                                <form action="{{ route('raffleOrganizations.autoAssign') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold py-1 px-3 rounded flex items-center gap-1">
                                        <box-icon name='magic-wand' size="xs" color="white"></box-icon>
                                        Auto-Zuordnung
                                    </button>
                                </form>
                            @endif
                            <button onclick="document.getElementById('newOrgModal').classList.remove('hidden')" class="bg-green-600 hover:bg-green-700 text-white text-xs font-semibold py-1 px-3 rounded flex items-center gap-1">
                                <box-icon name='plus' size="xs" color="white"></box-icon>
                                Neu
                            </button>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4 border">
                        <p class="text-xs text-gray-700 mb-4">
                            Teams in der gleichen Organisation (Block) lösen gegenseitig den Zeitabstands-Malus aus.
                        </p>

                        <div class="space-y-6">
                            @foreach($organizations as $index => $org)
                                <div class="bg-white border-2 border-blue-200 rounded-lg shadow-sm overflow-hidden mb-6">
                                    <!-- Rubrik-Header -->
                                    <div class="bg-blue-600 px-4 py-2 flex items-center justify-between text-white">
                                        <div class="flex items-center gap-3">
                                            <box-icon name='bookmark' size="sm" color="white"></box-icon>
                                            <div>
                                                <div class="text-[10px] uppercase tracking-wider opacity-80 font-bold">Rubrik / Haupt-Team</div>
                                                <div class="font-bold text-lg leading-tight">
                                                    @if($org->rubrikTeam)
                                                        {{ $org->rubrikTeam->teamname }}
                                                        <span class="text-sm font-normal opacity-75">({{ $org->name }})</span>
                                                    @else
                                                        {{ $org->name }}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex gap-2">
                                            <button onclick="editOrg({{ $org->id }}, '{{ $org->name }}')" class="bg-blue-500 hover:bg-blue-400 p-1 rounded transition" title="Name bearbeiten">
                                                <box-icon name='edit-alt' size="xs" color="white"></box-icon>
                                            </button>
                                            <form action="{{ route('raffleOrganizations.destroy', $org->id) }}" method="POST" onsubmit="return confirm('Organisation löschen? Teams werden freigegeben.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="bg-red-500 hover:bg-red-400 p-1 rounded transition" title="Löschen">
                                                    <box-icon name='trash' size="xs" color="white"></box-icon>
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    <!-- Kriterien & Info -->
                                    <div class="bg-blue-50 px-4 py-1 border-b flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <span class="text-[10px] font-bold text-blue-800">Filter-Kriterien:</span>
                                            @if($org->criteria)
                                                <div class="flex gap-1">
                                                    @foreach($org->criteria as $criterion)
                                                        <span class="text-[9px] bg-white border border-blue-200 text-blue-600 px-1.5 py-0.5 rounded font-medium shadow-sm">{{ $criterion }}</span>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-[10px] italic text-gray-500">Keine (nur manuelle Zuweisung)</span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Team-Liste -->
                                    <table class="min-w-full text-xs">
                                        <thead class="bg-gray-50 border-b">
                                            <tr>
                                                <th class="px-4 py-1.5 text-left text-[10px] font-bold text-gray-400 uppercase">ID</th>
                                                <th class="px-4 py-1.5 text-left text-[10px] font-bold text-gray-400 uppercase">Teamname</th>
                                                <th class="px-4 py-1.5 text-left text-[10px] font-bold text-gray-400 uppercase">Gruppe</th>
                                                <th class="px-4 py-1.5 text-right text-[10px] font-bold text-gray-400 uppercase">Aktionen</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100">
                                            @foreach($org->teams as $team)
                                                <tr class="hover:bg-blue-50 transition-colors {{ $org->rubrik_team_id == $team->id ? 'bg-yellow-50/50' : '' }}">
                                                    <td class="px-4 py-2 text-gray-400 font-mono">{{ $team->id }}</td>
                                                    <td class="px-4 py-2">
                                                        <div class="flex items-center gap-2">
                                                            <span class="font-bold text-gray-900">{{ $team->teamname }}</span>
                                                            @if($org->rubrik_team_id == $team->id)
                                                                <span class="text-[8px] bg-yellow-400 text-yellow-900 px-1 rounded uppercase font-black">Rubrik</span>
                                                            @endif
                                                        </div>
                                                        <div class="text-[10px] text-gray-500">{{ $team->verein ?: $team->ort }}</div>
                                                    </td>
                                                    <td class="px-4 py-2">
                                                        <span class="px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 font-medium">{{ optional($team->teamWertungsGruppe)->typ }}</span>
                                                    </td>
                                                    <td class="px-4 py-2 text-right">
                                                        <div class="flex justify-end items-center gap-3">
                                                            @if($org->rubrik_team_id != $team->id)
                                                                <form action="{{ route('raffleOrganizations.setRubrik', $org->id) }}" method="POST" class="inline">
                                                                    @csrf
                                                                    <input type="hidden" name="rubrik_team_id" value="{{ $team->id }}">
                                                                    <button type="submit" title="Als Rubrik-Team festlegen" class="text-gray-400 hover:text-yellow-600 transition">
                                                                        <box-icon name='bookmark-plus' size="xs" color="currentColor"></box-icon>
                                                                    </button>
                                                                </form>
                                                            @endif
                                                            <form action="{{ route('raffleOrganizations.assignTeam') }}" method="POST" class="inline">
                                                                @csrf
                                                                <input type="hidden" name="team_id" value="{{ $team->id }}">
                                                                <input type="hidden" name="organization_id" value="">
                                                                <button type="submit" title="Aus Organisation entfernen" class="text-gray-400 hover:text-red-500 transition">
                                                                    <box-icon name='x-circle' size="xs" color="currentColor"></box-icon>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    @if($unassignedTeams->isNotEmpty())
                                        <div class="p-2 bg-gray-50 border-t">
                                            <form action="{{ route('raffleOrganizations.assignTeam') }}" method="POST" class="flex gap-2">
                                                @csrf
                                                <input type="hidden" name="organization_id" value="{{ $org->id }}">
                                                <select name="team_id" required class="text-[10px] border-gray-300 rounded p-1 flex-grow">
                                                    <option value="">+ Team hinzufügen...</option>
                                                    @foreach($unassignedTeams as $ut)
                                                        <option value="{{ $ut->id }}">{{ $ut->teamname }} ({{ $ut->verein ?: $ut->ort ?: 'ID: '.$ut->id }})</option>
                                                    @endforeach
                                                </select>
                                                <button type="submit" class="bg-blue-500 text-white text-[10px] px-2 rounded">OK</button>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            @endforeach

                            @if($unassignedTeams->isNotEmpty() && $organizations->isNotEmpty())
                                <div class="mt-4">
                                    <h4 class="text-xs font-bold text-gray-500 uppercase mb-2">Nicht zugeordnete Teams</h4>
                                    <div class="bg-white border rounded p-2">
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($unassignedTeams as $ut)
                                                <div class="text-[10px] bg-gray-100 border rounded px-2 py-1 flex items-center gap-2">
                                                    <span>{{ $ut->teamname }}</span>
                                                    <span class="text-gray-400">ID: {{ $ut->id }}</span>
                                                    <form action="{{ route('raffleOrganizations.createFromTeam') }}" method="POST" class="inline">
                                                        @csrf
                                                        <input type="hidden" name="team_id" value="{{ $ut->id }}">
                                                        <button type="submit" title="Neue Rubrik aus diesem Team erstellen" class="text-blue-400 hover:text-blue-600 transition flex items-center">
                                                            <box-icon name='bookmark-plus' size="xs" color="currentColor"></box-icon>
                                                        </button>
                                                    </form>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if($organizations->isEmpty() && $unassignedTeams->isEmpty())
                                <div class="text-center py-8 text-gray-500 italic text-sm">
                                    Keine Teams für diese Regatta gefunden.
                                </div>
                            @elseif($organizations->isEmpty())
                                <div class="bg-blue-50 border border-blue-200 rounded p-4 text-center">
                                    <p class="text-sm text-blue-800 mb-3">Bisher sind keine Organisationen definiert.</p>
                                    <form action="{{ route('raffleOrganizations.autoAssign') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-xs">
                                            Automatische Erstzuordnung starten
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Modals for CRUD -->
                    <div id="newOrgModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                        <div class="bg-white rounded-lg p-6 w-96">
                            <h3 class="font-bold mb-4">Neue Organisation</h3>
                            <form action="{{ route('raffleOrganizations.store') }}" method="POST">
                                @csrf
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700">Name</label>
                                    <input type="text" name="name" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                </div>
                                <div class="flex justify-end gap-2">
                                    <button type="button" onclick="document.getElementById('newOrgModal').classList.add('hidden')" class="bg-gray-200 px-4 py-2 rounded text-sm">Abbrechen</button>
                                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded text-sm">Speichern</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div id="editOrgModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                        <div class="bg-white rounded-lg p-6 w-96">
                            <h3 class="font-bold mb-4">Organisation bearbeiten</h3>
                            <form id="editOrgForm" action="" method="POST">
                                @csrf
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700">Name</label>
                                    <input type="text" name="name" id="editOrgName" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                </div>
                                <div class="flex justify-end gap-2">
                                    <button type="button" onclick="document.getElementById('editOrgModal').classList.add('hidden')" class="bg-gray-200 px-4 py-2 rounded text-sm">Abbrechen</button>
                                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded text-sm">Speichern</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <script>
                        function editOrg(id, name) {
                            const modal = document.getElementById('editOrgModal');
                            const form = document.getElementById('editOrgForm');
                            const input = document.getElementById('editOrgName');

                            form.action = `/Regatta/Rennplan-Logik/Organizations/${id}`;
                            input.value = name;
                            modal.classList.remove('hidden');
                        }
                    </script>
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
                                    $teamToOrgTeams = [];
                                    foreach($organizations as $org) {
                                        $teamIds = $org->teams->pluck('id')->toArray();
                                        foreach($teamIds as $tid) {
                                            $teamToOrgTeams[$tid] = $org->teams;
                                        }
                                    }
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

                                                                    @if(isset($teamToOrgTeams[$row['team_id']]))
                                                                        @php
                                                                            $teamId = $row['team_id'];
                                                                            $orgTeams = $teamToOrgTeams[$teamId];
                                                                            $currentTime = \Carbon\Carbon::createFromFormat('H:i', $row['time']);

                                                                            $intervals = [];
                                                                            foreach($orgTeams as $otherTeam) {
                                                                                if ($otherTeam->id == $teamId) continue;

                                                                                // Suche den zeitlich engsten Vorher-Start eines anderen Teams dieser Organisation
                                                                                $lastOtherStart = collect($previewData)
                                                                                    ->where('team_id', $otherTeam->id)
                                                                                    ->map(fn($r) => \Carbon\Carbon::createFromFormat('H:i', $r['time']))
                                                                                    ->filter(fn($time) => $time->lt($currentTime))
                                                                                    ->sortByDesc(fn($time) => $time->timestamp)
                                                                                    ->first();

                                                                                if ($lastOtherStart) {
                                                                                    $diff = $currentTime->diffInMinutes($lastOtherStart);
                                                                                    $intervals[$otherTeam->id] = ['name' => $otherTeam->teamname, 'min_diff' => $diff];
                                                                                }
                                                                            }
                                                                        @endphp
                                                                        @if(!empty($intervals))
                                                                            <div class="flex flex-wrap gap-x-2 mt-1 border-t border-blue-50 pt-0.5">
                                                                                @foreach($intervals as $iData)
                                                                                    <span class="text-[8px] {{ $iData['min_diff'] < 30 ? 'text-red-500 font-bold' : 'text-blue-500' }}">
                                                                                        {{ $iData['name'] }}: {{ $iData['min_diff'] }} Min
                                                                                    </span>
                                                                                @endforeach
                                                                            </div>
                                                                        @endif
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

                                                                    @if(isset($teamToOrgTeams[$l['team_id']]))
                                                                        @php
                                                                            $teamId = $l['team_id'];
                                                                            $orgTeams = $teamToOrgTeams[$teamId];
                                                                            $currentTime = \Carbon\Carbon::createFromFormat('H:i', $l['time']);

                                                                            $intervals = [];
                                                                            foreach($orgTeams as $otherTeam) {
                                                                                if ($otherTeam->id == $teamId) continue;

                                                                                // Suche den zeitlich engsten Vorher-Start eines anderen Teams dieser Organisation
                                                                                $lastOtherStart = collect($previewData)
                                                                                    ->where('team_id', $otherTeam->id)
                                                                                    ->map(fn($r) => \Carbon\Carbon::createFromFormat('H:i', $r['time']))
                                                                                    ->filter(fn($time) => $time->lt($currentTime))
                                                                                    ->sortByDesc(fn($time) => $time->timestamp)
                                                                                    ->first();

                                                                                if ($lastOtherStart) {
                                                                                    $diff = $currentTime->diffInMinutes($lastOtherStart);
                                                                                    $intervals[$otherTeam->id] = ['name' => $otherTeam->teamname, 'min_diff' => $diff];
                                                                                }
                                                                            }
                                                                        @endphp
                                                                        @if(!empty($intervals))
                                                                            <div class="flex flex-wrap gap-x-2 ml-5 mt-0.5 border-t border-blue-50 pt-0.5">
                                                                                @foreach($intervals as $iData)
                                                                                    <span class="text-[8px] {{ $iData['min_diff'] < 30 ? 'text-red-500 font-bold' : 'text-blue-500' }}">
                                                                                        {{ $iData['name'] }}: {{ $iData['min_diff'] }} Min
                                                                                    </span>
                                                                                @endforeach
                                                                            </div>
                                                                        @endif
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
