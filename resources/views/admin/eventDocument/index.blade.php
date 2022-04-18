<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Bericht - Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

                <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                    <div class="mt-8 text-2xl">
                        Dokumente vom Event {{ $event->ueberschrift }}
                    </div>

                    <div class="mt-6 text-gray-500">
                        Dokumente
                        <!-- ToDo: Beschreibung erstellen -->
                    </div>

                </div>

                <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-2">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">Dokumente</div>
                        </div>

                        <div class="ml-12">
                            <div class="mt-2 text-sm text-gray-500">

                                <div style="text-align: left">
                                    <div>
                                        @if(session()->has('success'))
                                            <div class="p-3 bg-green-300 text-green-800 rounded shadow-sm">
                                                {!! session('success') !!}
                                            </div>
                                        @endif
                                    </div>

                                    <div class="my-4 flex">
                                        <a href="{{ url('EventDokumente/neu/'.$event->id) }}"><box-icon name='plus'></box-icon></a>
                                    </div>

                                    @foreach ( $reports as $report )
                                        <div class="rounded border shadow p-3 my-2 bg-blue-200">
                                            <div class="justify-between my-2">
                                                <div>
                                                     <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('EventDokumente/edit/'.$report->id) }}">
                                                        <box-icon name='edit'></box-icon>
                                                     </a>
                                                     @if($report['visible']==1)
                                                        <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('EventDokumente/inaktiv/'.$report->id) }}">
                                                            <box-icon name='show'></box-icon>
                                                        </a>
                                                     @endif
                                                     @if($report['visible']==0)
                                                        <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('EventDokumente/aktiv/'.$report->id) }}">
                                                            <box-icon name='hide'></box-icon>
                                                        </a>
                                                     @endif
                                                     @if($report['webseite']==0)
                                                        <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('EventDokumente/webaktiv/'.$report->id) }}">
                                                            <box-icon name='home'></box-icon>
                                                        </a>
                                                     @endif
                                                     @if($report['webseite']==1)
                                                        <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('EventDokumente/webinaktiv/'.$report->id) }}">
                                                            <box-icon name='world'></box-icon>
                                                            <!--<box-icon name='globe'></box-icon>-->
                                                        </a>
                                                     @endif
                                                     @if($report['event_id']==0)
                                                        <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('EventDokumente/softDelete/'.$report->id) }}">
                                                            <box-icon name='x-square'></box-icon>
                                                        </a>
                                                     @endif
                                                     @if($report['position'] != 10)
                                                        <a href="{{ url('EventDokumente/maxtop/'.$report->id) }}">
                                                            <box-icon name='chevrons-up' ></box-icon>
                                                        </a>
                                                        <a href="{{ url('EventDokumente/top/'.$report->id) }}">
                                                            <box-icon name='chevron-up'></box-icon>
                                                        </a>
                                                     @endif
                                                     @if($report->id != $reportMaxID)
                                                        <a href="{{ url('EventDokumente/down/'.$report->id) }}">
                                                            <box-icon name='chevron-down' ></box-icon>
                                                        </a>
                                                        <a href="{{ url('EventDokumente/maxdown/'.$report->id) }}">
                                                            <box-icon name='chevrons-down' ></box-icon>
                                                        </a>
                                                     @endif
                                                <!-- Note: && $report->image == Null ist überfüssig wenn keine alten Daten übernommen wurden-->
                                                     @if($report->bild == Null && $report->image == Null)
                                                        <a href="{{ url('EventDokumente/Eintrag/geloescht/'.$report->id) }}">
                                                            <box-icon name='x'></box-icon>
                                                        </a>
                                                     @endif
                                                </div>
                                                <div class="flex">
                                                    <p class="font-bold text-lg">
                                                     @php
                                                         $typOption=[
                                                            '2'  => 'Ausschreibung',
                                                            '3'  => 'Programm',
                                                            '4'  => 'Ergebnisse',
                                                            '5'  => 'Plakat / Flyer'
                                                        ];
                                                     @endphp
                                                        {{ $typOption[$report->verwendung] }}
                                                        <br>
                                                        {{ $report->titel }}
                                                    </p>
                                                    <p class="mx-3 py-1 text-xs text-gray-500 font-semibold">{{ $report->updated_at->diffForHumans() }}</p>
                                                </div>
                                            </div>
                                            @if($report->bild <> '')
                                                <a href="/storage/eventDokumente/{{ $report->bild }}" target="_blank">{{ $report->filename }}</a>
                                                <a href="{{ url('EventDokumente/geloescht/'.$report->id) }}">
                                                    <box-icon name='x'></box-icon>
                                                </a>
                                            @endif
                                        <!-- Note: Ist überfüssig wenn keine alten Daten übernommen wurden-->
                                            @if($report->image <> '')
                                                <a href="/daten/bilder/{{ $report->image }}" target="_blank">{{ $report->filename }}</a>
                                                <a href="{{ url('EventDokumente/alt/geloescht/'.$report->id) }}">
                                                    <box-icon name='x'></box-icon>
                                                </a>
                                            @endif
                                        </div>
                                    @endforeach

                                    {{ $reports->links() }}

                                    <br>
                                    <a class="p-2 bg-blue-500 w-40 rounded shadow text-white" href="/Adminmenu"><i class="fas fa-arrow-circle-up"></i>Zurück</a>

                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
