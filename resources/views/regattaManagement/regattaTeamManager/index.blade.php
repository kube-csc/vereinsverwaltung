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
                    <h1 class="text-2xl font-bold">Mannschaften verwalten</h1>
                    <p class="text-sm text-gray-600 mt-1">
                        Regatta-ID: {{ $regattaId }}
                    </p>
                </div>

                <div class="shrink-0">
                    <a href="/Regattamenu" class="bg-gray-200 hover:bg-gray-300 text-gray-900 font-semibold py-2 px-4 rounded">
                        Zurück zum Regattamenü
                    </a>
                </div>
            </div>

            <div class="mt-6">
                <form method="GET" action="{{ route('regattaTeamManager.index') }}" class="space-y-4">
                    <div class="flex flex-col md:flex-row gap-3">
                        <div class="flex-1">
                            <label for="q" class="block font-semibold mb-1">Suche (Teamname ähnlich)</label>
                            <input id="q" name="q" type="text" value="{{ $query }}" class="form-input w-full" placeholder="z.B. Kanu-Club / KC Musterstadt" />
                        </div>

                        <div class="w-full md:w-1/3">
                            <label for="race_type_id" class="block font-semibold mb-1">Bootsklasse</label>
                            <select id="race_type_id" name="race_type_id" class="form-select w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">-- Alle --</option>
                                @foreach($raceTypes as $raceType)
                                    <option value="{{ $raceType->id }}" {{ $raceTypeId == $raceType->id ? 'selected' : '' }}>
                                        {{ $raceType->typ }} ({{ $raceType->id }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row gap-3 items-end">
                        <div class="flex-1">
                            <label for="teamlink_filter" class="block font-semibold mb-1">Teamlink</label>
                            <select id="teamlink_filter" name="teamlink_filter" class="form-select w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">Alle Teams dieser Regatta</option>
                                <option value="0" {{ $teamlinkFilter === '0' ? 'selected' : '' }}>Nur ohne Teamlink (0)</option>
                                <option value="all_db_0" {{ $teamlinkFilter === 'all_db_0' ? 'selected' : '' }}>Alle Datenbank-Teams ohne Teamlink (0)</option>
                                <option value="once" {{ $teamlinkFilter === 'once' ? 'selected' : '' }}>Teams mit nur einer Verwendung (teamlink)</option>
                            </select>
                        </div>

                        <div class="flex gap-2">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded">
                                Filtern
                            </button>
                            <a href="{{ route('regattaTeamManager.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-900 font-semibold py-2 px-4 rounded">
                                Zurücksetzen
                            </a>
                        </div>
                    </div>
                </form>

                <p class="text-xs text-gray-500 mt-2">
                    Hinweis: Diese Seite listet alle Teams der Regatta auf. Die Suche filtert nach <code>teamname</code> und <code>Bootsklasse</code>.
                </p>
            </div>

            <div class="mt-8">
                @if($regattaTeams->isEmpty())
                    <div class="p-4 bg-yellow-50 border border-yellow-200 rounded">
                        Keine Teams gefunden.
                    </div>
                @else
                    <div class="space-y-6">
                        @foreach($regattaTeams as $team)
                            <div class="border rounded-lg overflow-hidden mb-6">
                                <div class="bg-gray-100 px-4 py-3 flex items-center justify-between">
                                    <div>
                                        <div class="font-semibold text-lg text-blue-800">
                                            Team: {{ $team->teamname ?? '-' }} (#{{ $team->id }})
                                        </div>
                                        <div class="text-xs text-gray-600">
                                            Teamlink-ID: {{ $team->teamlink ?? '-' }}<br>
                                            PLZ: {{ $team->plz ?? '-' }} | Telefon: {{ $team->telefon ?? '-' }} | E-Mail: {{ $team->email ?? '-' }}
                                        </div>
                                        <div class="text-xs text-blue-600 italic mt-1">
                                            Regatta: {{ optional($team->regatta)->ueberschrift ?? '-' }}
                                            @if(optional($team->regatta)->datumvon)
                                                ({{ \Carbon\Carbon::parse($team->regatta->datumvon)->format('d.m.Y') }})
                                            @endif
                                            <br>
                                            Rennklasse: {{ optional($team->teamWertungsGruppe)->typ ?? '-' }} (#{{ $team->gruppe_id ?? '-' }})
                                            (Min: {{ optional($team->teamWertungsGruppe)->min ?? '-' }}, Max: {{ optional($team->teamWertungsGruppe)->max ?? '-' }}, Distanz: {{ optional($team->teamWertungsGruppe)->distanz ?? '-' }})<br>
                                            Bootstyp: {{ optional(optional($team->teamWertungsGruppe)->raceTypeTemplate)->typ ?? '-' }} (#{{ optional(optional($team->teamWertungsGruppe)->raceTypeTemplate)->id ?? '-' }})
                                            (Min: {{ optional(optional($team->teamWertungsGruppe)->raceTypeTemplate)->min ?? '-' }}, Max: {{ optional(optional($team->teamWertungsGruppe)->raceTypeTemplate)->max ?? '-' }}, Distanz: {{ optional(optional($team->teamWertungsGruppe)->raceTypeTemplate)->distanz ?? '-' }})
                                        </div>
                                    </div>
                                </div>

                                <div class="divide-y">

                                    @if(isset($team->andereRegatten) && $team->andereRegatten->isNotEmpty())
                                        <div class="px-4 py-3 bg-blue-50">
                                            <div class="text-sm font-semibold text-blue-900 mb-2">Andere Regatten dieses Teams:</div>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                @foreach($team->andereRegatten as $anderes)
                                                    <div class="text-xs border-l-2 border-blue-300 pl-2">
                                                        <div class="font-bold">
                                                            {{ optional($anderes->regatta)->ueberschrift ?? 'Unbekannte Regatta' }}
                                                            @if(optional($anderes->regatta)->datumvon)
                                                                ({{ \Carbon\Carbon::parse($anderes->regatta->datumvon)->format('d.m.Y') }})
                                                            @endif
                                                        </div>
                                                        <div>{{ $anderes->teamname }} (#{{ $anderes->id }})</div>
                                                        <div class="italic">
                                                            Rennklasse: {{ optional($anderes->teamWertungsGruppe)->typ ?? '-' }} (#{{ $anderes->gruppe_id ?? '-' }})
                                                            (Min: {{ optional($anderes->teamWertungsGruppe)->min ?? '-' }}, Max: {{ optional($anderes->teamWertungsGruppe)->max ?? '-' }}, Distanz: {{ optional($anderes->teamWertungsGruppe)->distanz ?? '-' }})<br>
                                                            Bootstyp: {{ optional(optional($anderes->teamWertungsGruppe)->raceTypeTemplate)->typ ?? '-' }} (#{{ optional(optional($anderes->teamWertungsGruppe)->raceTypeTemplate)->id ?? '-' }})
                                                            (Min: {{ optional(optional($anderes->teamWertungsGruppe)->raceTypeTemplate)->min ?? '-' }}, Max: {{ optional(optional($anderes->teamWertungsGruppe)->raceTypeTemplate)->max ?? '-' }}, Distanz: {{ optional(optional($anderes->teamWertungsGruppe)->raceTypeTemplate)->distanz ?? '-' }})
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

