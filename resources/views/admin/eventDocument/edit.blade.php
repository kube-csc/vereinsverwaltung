<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Eventdokumente - Dashboard') }}
        </h2>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

                <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                    <div class="mt-8 text-2xl">
                        Event: {{ $event->ueberschrift }}<br>
                        Datei: {{ old('reportTitleDocument') ?? $report->titel }}
                    </div>

                    <div class="mt-6 text-gray-500">
                        @php
                            // ToDo: Beschreibungstext überarbeiten
                        @endphp
                        Bitte gebe die Daten der Datei ein.
                    </div>
                </div>

                <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-2">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">Datei bearbeiten</div>
                        </div>

                        <div class="ml-12">
                            <div class="mt-2 text-sm text-gray-500">

                                <form autocomplete="off" action="{{ url('EventDokumente/update/'.$report->id) }}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    @php
                                        // ToDo:  @method('PUT') in Hobby Projekt noch mal erlernen
                                    @endphp
                                    <div class="my-4" >
                                        <label for="name">Titel der Datei:</label>
                                        <input type="text" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('reportTitleDocument') ? 'bg-red-300' : '' }}"
                                               id="reportTitleDocument" placeholder="Titel" name="reportTitleDocument" value="{{ old('reportTitleDocument') ?? $report->titel }}">
                                        <small class="form-text text-danger">{!! $errors->first('reportTitleDocument') !!}</small>
                                    </div>
                                    <div class="my-4" >
                                        <label for="name">Datei:</label>
                                        @if($report->bild)
                                            <a href="/storage/eventDokumente/{{$report->bild}}" target="_blank">{{$report->filename}}</a>
                                        @endif
                                    <!-- Note: Ist überfüssig wenn keine alten Daten übernommen wurden-->
                                        @if($report->image)
                                            <a href="/daten/text/{{$report->image}}" target="_blank">{{$report->filename}}</a>
                                        @endif
                                        <input type="file" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('documentFile') ? 'bg-red-300' : '' }}"
                                               id="documentFile" name="documentFile" value="">
                                        <small class="form-text text-danger">{!! $errors->first('documentFile') !!}</small>
                                    </div>
                                    <div class="my-4" >
                                        <label for="verwendung">Dokumententyp:</label>
                                        <br>
                                        <select name="verwendung" class="w-full border rounded shadow p-2 mr-2 my-2">
                                            <option value="2"
                                                @if($report->verwendung == "2")
                                                    selected
                                                @endif
                                                >Ausschreibung
                                            </option>
                                            <option value="3"
                                                @if($report->verwendung == "3")
                                                    selected
                                                @endif
                                                >Programm
                                            </option>
                                            <option value="4"
                                                @if($report->verwendung == "4")
                                                    selected
                                                @endif
                                                >Ergebnisse
                                            </option>
                                            <option value="5"
                                                @if($report->verwendung == "5")
                                                    selected
                                                @endif
                                                >Plakat / Flyer
                                            </option>
                                        </select>
                                    </div>
                                    <div class="my-4" >
                                        <label for="name">Kommentar der Datei:</label>
                                        <textarea rows="15" cols="150" name="reportDocumentComment" class="w-full rounded border shadow p-2 mr-2 my-2">{{ old('reportDocumentComment') ?? $report->kommentar }}</textarea>
                                        <small class="form-text text-danger">{!! $errors->first('reportDocumentComment') !!}</small>
                                    </div>
                                    <div class="py-2">
                                        <button type="submit" class="p-2 bg-blue-500 w-40 rounded shadow text-white">Änderungen speichern</button>
                                    </div>
                                </form>
                                <br>
                                <a class="p-2 bg-blue-500 w-40 rounded shadow text-white" href="/EventDokumente/{{ $report->event_id  }}"><i class="fas fa-arrow-circle-up"></i>Zurück</a>
                            </div>
                        </div>

                    </div>
                </div>

            </div>

        </div>
    </div>

</x-app-layout>
