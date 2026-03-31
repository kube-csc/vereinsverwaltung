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
                    <h1 class="text-2xl font-bold">Mannschaften (teamlink) verwalten</h1>
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
                <form method="GET" action="{{ route('regattaTeamManager.index') }}" class="flex flex-col md:flex-row gap-3 md:items-end">
                    <div class="flex-1">
                        <label for="q" class="block font-semibold mb-1">Suche (Teamname ähnlich)</label>
                        <input id="q" name="q" type="text" value="{{ $query }}" class="form-input w-full" placeholder="z.B. Kanu-Club / KC Musterstadt" />
                    </div>

                    <div>
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

                    <div class="flex gap-2">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded">
                            Filtern
                        </button>
                        <a href="{{ route('regattaTeamManager.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-900 font-semibold py-2 px-4 rounded">
                            Zurücksetzen
                        </a>
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
                                            Teamlink-ID: {{ $team->teamlink ?? '-' }}
                                        </div>
                                        <div class="text-xs text-blue-600 italic">
                                            Bootstyp: {{ optional(optional($team->teamWertungsGruppe)->raceTypeTemplate)->typ ?? '-' }} (#{{ $team->gruppe_id ?? '-' }})
                                        </div>
                                    </div>
                                </div>

                                <div class="divide-y">
                                    <div class="px-4 py-3 flex flex-col md:flex-row md:items-center md:justify-between gap-2">
                                        <div>
                                            <div class="font-semibold">{{ $team->teamname }} (#{{ $team->id }})</div>
                                            <div class="text-xs text-gray-600">
                                                Wertungsart: {{ optional($team->teamWertungsGruppe)->typ ?? '-' }} | Gruppe-ID: {{ $team->gruppe_id }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                Regatta: {{ optional($team->regatta)->ueberschrift ?? '-' }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                PLZ: {{ $team->plz ?? '-' }} | Telefon: {{ $team->telefon ?? '-' }} | E-Mail: {{ $team->email ?? '-' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

