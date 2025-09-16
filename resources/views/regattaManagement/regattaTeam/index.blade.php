<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Regattaverwaltung') }}: {{ Session::get('regattaSelectUeberschrift') }}
        </h2>
    </x-slot>
    <div class="max-w-4xl mx-auto py-8">
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Regatta Teams</h1>
                <div class="flex items-center gap-4">
                    <span class="text-gray-700 text-base font-semibold" title="Anzahl gemeldeter Teams">
                        Meldungen: {{ $regattaTeams->total() }}
                    </span>
                    <a href="{{ route('regattaTeam.werbungsquelle') }}"
                       class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded flex items-center justify-center transition duration-150 ease-in-out"
                       title="Meldung Werbungsquelle">
                        <box-icon name='pie-chart-alt'></box-icon>
                        <span class="ml-2 hidden sm:inline">Meldung Werbungsquelle</span>
                    </a>
                    <a href="{{ route('regattaTeam.create') }}"
                       class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded flex items-center justify-center"
                       title="Neues Team melden">
                        <box-icon name='plus'></box-icon>
                    </a>
                </div>
            </div>
            <div>
                @forelse($regattaTeams as $regattaTeam)
                    <div class="rounded border shadow p-3 my-2 bg-blue-200">
                        <div class="flex justify-between items-center">
                            <div class="flex items-center">
                                <a href="{{ route('regattaTeam.show', $regattaTeam->id) }}">
                                    <box-icon name='group' type='solid' class="mr-2"></box-icon>
                                </a>
                                <div>
                                    <p class="font-bold text-lg">
                                        {{ $regattaTeam->teamname }}
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        Wertungsart: {{ $regattaTeam->teamWertungsGruppe->typ }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        Meldedatum: {{ $regattaTeam->datum ? \Carbon\Carbon::parse($regattaTeam->datum)->format('d.m.Y') : '-' }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        Status: {{ $regattaTeam->status ?? '-' }}
                                    </p>
                                    @if($regattaTeam->updated_at)
                                        <p class="text-xs text-gray-500">
                                            Zuletzt geändert: {{ $regattaTeam->updated_at->diffForHumans() }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <a href="{{ route('regattaTeam.edit', $regattaTeam->id) }}"
                                   class="ml-2 btn btn-sm btn-outline-primary text-blue-500 hover:underline">
                                    <box-icon name='edit' type='solid'></box-icon>
                                </a>
                                <!-- Weitere Aktionen nach Bedarf -->
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="py-4 text-center text-gray-500">Keine Teams vorhanden.</div>
                @endforelse

                {{-- Pagination auf Deutsch --}}
                @if ($regattaTeams->hasPages())
                    <div class="mt-4 flex justify-center items-center gap-4">
                        <nav>
                            <ul class="inline-flex items-center -space-x-px">
                                {{-- Vorherige Seite --}}
                                @if ($regattaTeams->onFirstPage())
                                    <li>
                                        <span class="px-3 py-2 ml-0 leading-tight text-gray-400 bg-white border border-gray-300 rounded-l-lg">Zurück</span>
                                    </li>
                                @else
                                    <li>
                                        <a href="{{ $regattaTeams->previousPageUrl() }}" rel="prev" class="px-3 py-2 ml-0 leading-tight text-gray-500 bg-white border border-gray-300 rounded-l-lg hover:bg-gray-100 hover:text-gray-700">Zurück</a>
                                    </li>
                                @endif

                                {{-- Seitenzahlen --}}
                                @foreach ($regattaTeams->getUrlRange(1, $regattaTeams->lastPage()) as $page => $url)
                                    @if ($page == $regattaTeams->currentPage())
                                        <li>
                                            <span class="px-3 py-2 leading-tight text-blue-600 bg-blue-50 border border-gray-300">{{ $page }}</span>
                                        </li>
                                    @else
                                        <li>
                                            <a href="{{ $url }}" class="px-3 py-2 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700">{{ $page }}</a>
                                        </li>
                                    @endif
                                @endforeach

                                {{-- Nächste Seite --}}
                                @if ($regattaTeams->hasMorePages())
                                    <li>
                                        <a href="{{ $regattaTeams->nextPageUrl() }}" rel="next" class="px-3 py-2 leading-tight text-gray-500 bg-white border border-gray-300 rounded-r-lg hover:bg-gray-100 hover:text-gray-700">Weiter</a>
                                    </li>
                                @else
                                    <li>
                                        <span class="px-3 py-2 leading-tight text-gray-400 bg-white border border-gray-300 rounded-r-lg">Weiter</span>
                                    </li>
                                @endif
                            </ul>
                        </nav>
                        <a href="/Regattamenu" class="p-2 bg-blue-500 rounded shadow text-white">Zurück</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
