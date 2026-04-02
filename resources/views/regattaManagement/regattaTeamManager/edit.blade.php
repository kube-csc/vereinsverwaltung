<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Regattaverwaltung') }}: {{ Session::get('regattaSelectUeberschrift') }} - Teamlink bearbeiten
        </h2>
    </x-slot>

    <div class="max-w-4xl mx-auto py-8">
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-start justify-between gap-4 mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-blue-800">Teamlink bearbeiten</h1>
                    <p class="text-sm text-gray-600 mt-1">
                        Team: <strong>{{ $team->teamname }}</strong> (#{{ $team->id }})
                    </p>
                </div>

                <div class="shrink-0">
                    <a href="{{ route('regattaTeamManager.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-900 font-semibold py-2 px-4 rounded">
                        Abbrechen / Zurück
                    </a>
                </div>
            </div>

            <div class="border rounded-lg p-4 bg-gray-50 mb-8">
                <div class="text-sm font-semibold text-gray-700 mb-2 underline">Aktuelle Team-Details:</div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                    <div>
                        <span class="font-bold">Team-ID:</span> #{{ $team->id }}<br>
                        <span class="font-bold">Teamlink-ID:</span> {{ $team->teamlink ?? '0 (nicht verknüpft)' }}<br>
                        <span class="font-bold">Regatta:</span> {{ optional($team->regatta)->ueberschrift ?? '-' }}
                        @if(optional($team->regatta)->datumvon)
                            ({{ \Carbon\Carbon::parse($team->regatta->datumvon)->format('d.m.Y') }})
                        @endif
                        @if($team->regatta_id)
                            <a href="{{ url('/Regatta/'.$team->regatta_id) }}" title="Diese Regatta auswählen" class="ml-1 inline-block align-middle text-blue-600 hover:text-blue-800">
                                <box-icon name='pin' size='xs'></box-icon>
                            </a>
                        @endif
                        <br>
                        <span class="font-bold">PLZ:</span> {{ $team->plz ?? '-' }}<br>
                        <span class="font-bold">Email:</span> {{ $team->email ?? '-' }}<br>
                        <span class="font-bold">Telefon:</span> {{ $team->telefon ?? '-' }}
                    </div>
                    <div class="flex flex-col justify-between">
                        <div>
                            <span class="font-bold">Rennklasse:</span> {{ optional($team->teamWertungsGruppe)->typ }} (#{{ $team->gruppe_id }})
                            (Min: {{ optional($team->teamWertungsGruppe)->min ?? '-' }}, Max: {{ optional($team->teamWertungsGruppe)->max ?? '-' }}, Distanz: {{ optional($team->teamWertungsGruppe)->distanz ?? '-' }})<br>
                            <span class="font-bold">Bootstyp:</span> {{ optional(optional($team->teamWertungsGruppe)->raceTypeTemplate)->typ }} (#{{ optional(optional($team->teamWertungsGruppe)->raceTypeTemplate)->id }})
                            (Min: {{ optional(optional($team->teamWertungsGruppe)->raceTypeTemplate)->min ?? '-' }}, Max: {{ optional(optional($team->teamWertungsGruppe)->raceTypeTemplate)->max ?? '-' }}, Distanz: {{ optional(optional($team->teamWertungsGruppe)->raceTypeTemplate)->distanz ?? '-' }})
                        </div>
                    </div>
                </div>
            </div>

            <form action="{{ route('regattaTeamManager.update', $team->id) }}" method="POST">
                @csrf
                <div class="mb-6">
                    <label for="teamlink" class="block font-semibold mb-2">Teamlink-ID setzen</label>
                    <div class="flex gap-2">
                        <input type="number" name="teamlink" id="teamlink" value="{{ $team->teamlink }}" class="form-input w-full md:w-1/3 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="Z.B. 123">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded">
                            Speichern
                        </button>
                        <a href="{{ route('regattaTeamManager.edit', $team->id) }}?assign_new_teamlink=1"
                           class="bg-blue-100 text-blue-700 border border-blue-300 px-3 py-2 rounded font-semibold hover:bg-blue-200 transition">
                            <box-icon name='sparkles' size='xs'></box-icon> Neuen Teamlink (#{{ $nextFreeTeamlink }}) vergeben
                        </a>
                    </div>
                    <p class="text-xs text-gray-500 mt-2 italic">
                        Hinweis: Geben Sie <strong>0</strong> ein, um die Verknüpfung zu lösen.
                    </p>
                </div>
            </form>

            <div class="mt-10">
                @if($linkedTeams->isNotEmpty())
                    <h3 class="text-lg font-bold mb-4 border-b pb-2 text-green-700">Teams mit gleichem Teamlink (#{{ $team->teamlink }})</h3>
                    <div class="space-y-4 mb-10">
                        @foreach($linkedTeams as $linked)
                            <div class="border rounded-lg overflow-hidden border-green-200">
                                <div class="bg-green-50 px-4 py-3 flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="font-semibold text-lg text-green-800">
                                            Team: {{ $linked->teamname ?? '-' }} (#{{ $linked->id }})
                                        </div>
                                        <div class="text-xs text-gray-600">
                                            PLZ: {{ $linked->plz ?? '-' }} | Telefon: {{ $linked->telefon ?? '-' }} | E-Mail: {{ $linked->email ?? '-' }}
                                        </div>
                                        <div class="text-xs text-green-600 italic mt-1">
                                            Regatta: {{ optional($linked->regatta)->ueberschrift ?? '-' }}
                                            @if(optional($linked->regatta)->datumvon)
                                                ({{ \Carbon\Carbon::parse($linked->regatta->datumvon)->format('d.m.Y') }})
                                            @endif
                                            <br>
                                            Rennklasse: {{ optional($linked->teamWertungsGruppe)->typ ?? '-' }} (#{{ $linked->gruppe_id ?? '-' }})
                                            (Min: {{ optional($linked->teamWertungsGruppe)->min ?? '-' }}, Max: {{ optional($linked->teamWertungsGruppe)->max ?? '-' }}, Distanz: {{ optional($linked->teamWertungsGruppe)->distanz ?? '-' }})<br>
                                            Bootstyp: {{ optional(optional($linked->teamWertungsGruppe)->raceTypeTemplate)->typ ?? '-' }} (#{{ optional(optional($linked->teamWertungsGruppe)->raceTypeTemplate)->id ?? '-' }})
                                            (Min: {{ optional(optional($linked->teamWertungsGruppe)->raceTypeTemplate)->min ?? '-' }}, Max: {{ optional(optional($linked->teamWertungsGruppe)->raceTypeTemplate)->max ?? '-' }}, Distanz: {{ optional(optional($linked->teamWertungsGruppe)->raceTypeTemplate)->distanz ?? '-' }})
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <a href="{{ route('regattaTeamManager.edit', $linked->id) }}"
                                           title="Dieses Team bearbeiten"
                                           class="inline-flex items-center p-2 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            <box-icon name='edit-alt' size='xs' class="text-blue-600"></box-icon>
                                            <span class="ml-2 text-xs">Bearbeiten</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <h3 class="text-lg font-bold mb-4 border-b pb-2">Vorschläge (Gleicher Bootstyp & ähnliche Merkmale)</h3>

                <!-- Filter für Vorschläge -->
                <div class="bg-blue-50 p-4 rounded-lg mb-6">
                    <form method="GET" action="{{ route('regattaTeamManager.edit', $team->id) }}" class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
                        <div>
                            <label for="q_team" class="block text-xs font-semibold mb-1 text-gray-700">Teamname</label>
                            <input type="text" name="q_team" id="q_team" value="{{ $q_team }}" placeholder="Suche Name..."
                                   class="form-input w-full text-xs rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        </div>
                        <div>
                            <label for="q_plz" class="block text-xs font-semibold mb-1 text-gray-700">PLZ</label>
                            <select name="q_plz" id="q_plz"
                                    class="form-select w-full text-xs rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <option value="">-- Alle --</option>
                                @foreach($plz_options as $option)
                                    <option value="{{ $option }}" {{ $q_plz == $option ? 'selected' : '' }}>
                                        {{ $option }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="q_bootstyp" class="block text-xs font-semibold mb-1 text-gray-700">Bootstyp</label>
                            <select name="q_bootstyp" id="q_bootstyp"
                                    class="form-select w-full text-xs rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <option value="">-- Alle --</option>
                                @foreach($bootstypen as $bt)
                                    <option value="{{ $bt->id }}" {{ $q_bootstyp == $bt->id ? 'selected' : '' }}>
                                        {{ $bt->typ }} (#{{ $bt->id }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-3 rounded text-xs flex-1">
                                Filtern
                            </button>
                            <a href="{{ route('regattaTeamManager.edit', $team->id) }}" class="bg-gray-200 hover:bg-gray-300 text-gray-900 font-semibold py-2 px-3 rounded text-xs flex-1 text-center">
                                Reset
                            </a>
                        </div>
                    </form>
                </div>

                @if($suggestions->isEmpty())
                    <p class="text-gray-500 italic text-sm">Keine direkten Vorschläge in der Datenbank gefunden.</p>
                @else
                    <div class="space-y-4">
                        @foreach($suggestions as $suggestion)
                            <div class="border rounded-lg overflow-hidden mb-4">
                                <div class="bg-gray-100 px-4 py-3 flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="font-semibold text-lg text-blue-800">
                                            Team: {{ $suggestion->teamname ?? '-' }} (#{{ $suggestion->id }})
                                        </div>
                                        <div class="text-xs text-gray-600">
                                            Teamlink-ID: {{ $suggestion->teamlink ?? '-' }}<br>
                                            PLZ: {{ $suggestion->plz ?? '-' }} | Telefon: {{ $suggestion->telefon ?? '-' }} | E-Mail: {{ $suggestion->email ?? '-' }}
                                        </div>
                                        <div class="text-xs text-blue-600 italic mt-1">
                                            Regatta: {{ optional($suggestion->regatta)->ueberschrift ?? '-' }}
                                            @if(optional($suggestion->regatta)->datumvon)
                                                ({{ \Carbon\Carbon::parse($suggestion->regatta->datumvon)->format('d.m.Y') }})
                                            @endif
                                            <br>
                                            Rennklasse: {{ optional($suggestion->teamWertungsGruppe)->typ ?? '-' }} (#{{ $suggestion->gruppe_id ?? '-' }})
                                            (Min: {{ optional($suggestion->teamWertungsGruppe)->min ?? '-' }}, Max: {{ optional($suggestion->teamWertungsGruppe)->max ?? '-' }}, Distanz: {{ optional($suggestion->teamWertungsGruppe)->distanz ?? '-' }})<br>
                                            Bootstyp: {{ optional(optional($suggestion->teamWertungsGruppe)->raceTypeTemplate)->typ ?? '-' }} (#{{ optional(optional($suggestion->teamWertungsGruppe)->raceTypeTemplate)->id ?? '-' }})
                                            (Min: {{ optional(optional($suggestion->teamWertungsGruppe)->raceTypeTemplate)->min ?? '-' }}, Max: {{ optional(optional($suggestion->teamWertungsGruppe)->raceTypeTemplate)->max ?? '-' }}, Distanz: {{ optional(optional($suggestion->teamWertungsGruppe)->raceTypeTemplate)->distanz ?? '-' }})
                                        </div>
                                    </div>
                                    <div class="ml-4 flex flex-col gap-1.5 min-w-[200px]">
                                        <!-- Aktuelles Team übernimmt ID vom Vorschlag -->
                                        <form action="{{ route('regattaTeamManager.sync', $team->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="other_id" value="{{ $suggestion->id }}">
                                            <input type="hidden" name="direction" value="take">
                                            <button type="submit"
                                                    class="w-full text-left text-[9px] bg-green-100 text-green-700 border border-green-300 px-2 py-1 rounded font-semibold hover:bg-green-200 transition">
                                                <span class="block text-[10px] mb-0.5"><box-icon name='download' size='xs' class="align-middle"></box-icon> Bearbeitetes Team (#{{ $team->id }}) übernimmt Teamlink-ID {{ $suggestion->teamlink }}</span>
                                                <span class="block font-normal opacity-75 leading-tight">Vom Vorschlag: "{{ $suggestion->teamname }}" (#{{ $suggestion->id }})</span>
                                            </button>
                                        </form>

                                        <!-- Vorschlag übernimmt ID von aktuellem Team -->
                                        @if($team->teamlink > 0)
                                            <form action="{{ route('regattaTeamManager.sync', $team->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="other_id" value="{{ $suggestion->id }}">
                                                <input type="hidden" name="direction" value="give">
                                                <button type="submit"
                                                        class="w-full text-left text-[9px] bg-blue-100 text-blue-700 border border-blue-300 px-2 py-1 rounded font-semibold hover:bg-blue-200 transition">
                                                    <span class="block text-[10px] mb-0.5"><box-icon name='upload' size='xs' class="align-middle"></box-icon> Vorschlag (#{{ $suggestion->id }}) übernimmt Teamlink-ID {{ $team->teamlink }}</span>
                                                    <span class="block font-normal opacity-75 leading-tight">Vom bearbeiteten Team: "{{ $team->teamname }}" (#{{ $team->id }})</span>
                                                </button>
                                            </form>
                                        @endif

                                        <!-- Vorschlag bearbeiten -->
                                        <a href="{{ route('regattaTeamManager.edit', $suggestion->id) }}"
                                           class="w-full text-center text-[10px] bg-indigo-100 text-indigo-700 border border-indigo-300 px-2 py-1.5 rounded font-semibold hover:bg-indigo-200 transition leading-tight">
                                            <box-icon name='edit-alt' size='xs' class="align-middle"></box-icon> Vorschlag bearbeiten (#{{ $suggestion->id }})
                                        </a>
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
