<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Regatta Verwaltung') }} {{ Session::get('regattaSelectUeberschrift') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

                <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                    <div class="mt-8 text-2xl">
                        <label for="name">Tabelle:</label>
                        {{ $tabele->ueberschrift }}
                    </div>

                    <div class="mt-6 text-gray-500">
                        <label for="name">Wertungsart:</label>
                        @if((int) $tabele->wertungsart === 1)
                            Punktewertung
                        @elseif((int) $tabele->wertungsart === 2)
                            Zeitwertung
                        @elseif((int) $tabele->wertungsart === 3)
                            Einzelner Lauf
                        @else
                            Unbekannt
                        @endif
                    </div>

                </div>

                <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-2">
                    <div class="p-6">

                        <div class="flex items-center">
                            <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">
                               <label for="name">Tabelle:</label>
                               {{ $tabele->ueberschrift }}
                            </div>
                        </div>

                        <div class="ml-12">
                            <div class="mt-2 text-sm text-gray-500">
                                    @php
                                      $isZeitwertung    = (int) $tabele->wertungsart === 2;
                                      $isEinzelnerLauf  = (int) $tabele->wertungsart === 3;
                                    @endphp
                                    @foreach($tabeledatas as $tabeledata)
                                        @php
                                            $anzeigeWert = $isZeitwertung
                                                ? $tabeledata->zeit . ',' . str_pad((string) ((int) $tabeledata->hundert), 2, '0', STR_PAD_LEFT)
                                                : $tabeledata->punkte . ' Punkte';
                                        @endphp
                                       <div class="my-4" >
                                          <label for="name">Platz:</label>
                                                  {{ $tabeledata->platz ?? '-' }} {{ $tabeledata->getMannschaft->teamname }} {{ $anzeigeWert }}
                                                  @if(! $isEinzelnerLauf)
                                                      {{ $tabeledata->rennanzahl }}/{{ $tabele->maxrennen }} Rennanzahl
                                                  @endif
                                                  @if($tabele->buchholzwertungaktiv && ! $isZeitwertung && ! $isEinzelnerLauf)
                                                      {{ $tabeledata->buchholzzahl }} Buchholzzahl
                                                  @endif
                                       </div>
                                   @endforeach
                              <br>
                              <a class="p-2 bg-blue-500 w-40 rounded shadow text-white" href="/Tabelle/alle"><i class="fas fa-arrow-circle-up"></i>Zurück</a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
