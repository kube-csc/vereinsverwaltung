<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Konsistenzprüfung für Tabelle') }}: {{ $tabele->ueberschrift }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <h3 class="text-lg font-bold mb-4">Konsistenzprüfung der Mannschaftszuweisungen</h3>
                @if(count($auffaellig) == 0)
                    <div class="p-3 bg-green-200 text-green-800 rounded shadow-sm">
                        Alle Mannschaften sind korrekt ({{ $tabele->maxrennen }} mal) gesetzt.
                    </div>
                @else
                    <div class="p-3 bg-red-200 text-red-800 rounded shadow-sm mb-4">
                        Folgende Mannschaften sind nicht korrekt gesetzt:
                    </div>
                    <div class="w-full">
                        <!-- Kopfzeile -->
                        <div class="grid grid-cols-5 bg-gray-100 font-semibold border-b">
                            <div class="px-4 py-2">Mannschaft</div>
                            <div class="px-4 py-2">Status</div>
                            <div class="px-4 py-2">Gesetzt</div>
                            <div class="px-4 py-2">Soll</div>
                            <div class="px-4 py-2">Zu oft gesetzt?</div>
                        </div>
                        <!-- Datenzeilen -->
                        @foreach($auffaellig as $item)
                            <div class="grid grid-cols-5 border-b last:border-b-0">
                                <div class="border-r px-4 py-2 align-top">
                                    <a href="{{ route('regattaTeam.edit', $item['mannschaft']->id) }}" class="text-blue-600 underline" title="Team bearbeiten">
                                        {{ $item['mannschaft']->teamname }}
                                    </a>
                                </div>
                                <div class="border-r px-4 py-2 align-top">
                                    {{ $item['mannschaft']->status ?? '-' }}
                                </div>
                                <div class="border-r px-4 py-2 align-top">{{ $item['gesetzt'] }}</div>
                                <div class="border-r px-4 py-2 align-top">{{ $item['soll'] }}</div>
                                <div class="px-4 py-2 align-top">
                                    @if(isset($item['zu_viele_rennen']))
                                        <div>
                                            <span class="font-semibold">Zu oft gesetzt in Rennen:</span>
                                            <ul class="list-disc ml-4">
                                                @foreach($item['zu_viele_rennen'] as $rennen)
                                                    <li>
                                                        <a href="{{ url('/Teamverlosung/setzen/'.$rennen['id']) }}" class="text-blue-600 underline">
                                                            @php
                                                                $rennNummer = isset($rennen['nummer']) ? $rennen['nummer'] : null;
                                                            @endphp
                                                            {{ $rennen['name'] ? $rennen['name'] : 'Rennen #'.$rennen['id'] }}
                                                            @if($rennNummer)
                                                                <span class="ml-1 text-gray-500">[Nr. {{ $rennNummer }}]</span>
                                                            @endif
                                                        </a>
                                                        @if(!empty($rennen['datum']) || !empty($rennen['uhrzeit']))
                                                            <span class="ml-2 text-gray-600 text-sm">
                                                                @if(!empty($rennen['datum']))
                                                                    {{ \Carbon\Carbon::parse($rennen['datum'])->format('d.m.Y') }}
                                                                @endif
                                                                @if(!empty($rennen['uhrzeit']))
                                                                    {{ \Carbon\Carbon::parse($rennen['uhrzeit'])->format('H:i') }}
                                                                @endif
                                                            </span>
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @else
                                        &ndash;
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
                <div class="mt-6">
                    <a href="{{ route('tabele.index') }}" class="p-2 bg-blue-500 rounded shadow text-white">Zurück zur Übersicht</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
