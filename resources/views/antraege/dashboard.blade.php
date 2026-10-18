<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Antragsverwaltung
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <div class="rounded-xl shadow-lg overflow-hidden bg-gradient-to-r from-blue-700 to-blue-500 text-white">
                <div class="px-6 py-8 sm:px-10">
                    <p class="text-sm uppercase tracking-wide text-blue-100">Willkommen, {{ auth()->user()->vorname ?: auth()->user()->name }}</p>
                    <h3 class="mt-1 text-2xl sm:text-3xl font-bold">Förder- und Vereinsanträge im Überblick</h3>
                    <p class="mt-2 text-blue-100 max-w-2xl">
                        Verwalten Sie Förderprogramme, Fördertöpfe, Benutzer und Bankverbindungen an einem Ort.
                        Weitere Funktionen werden schrittweise ergänzt.
                    </p>
                </div>
            </div>

            @if(!empty($stats))
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach($stats as $stat)
                        <div class="bg-white rounded-xl shadow p-5 border-l-4 {{ $stat['border'] }}">
                            <p class="text-sm text-gray-500">{{ $stat['label'] }}</p>
                            <p class="mt-1 text-3xl font-bold text-gray-900">{{ $stat['value'] }}</p>
                            @if(!empty($stat['hint']))
                                <p class="mt-1 text-xs text-gray-500">{{ $stat['hint'] }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

            <div>
                <h4 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-3">Bereiche</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">

                    @can('antraege.verwalten')
                        <a href="{{ route('antraege.foerderprogramme') }}" class="group bg-gray-50 border border-gray-300 rounded-xl shadow-md hover:bg-white hover:border-blue-400 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 transition-all duration-200 p-6 flex items-start space-x-4">
                            <span class="flex-shrink-0 h-12 w-12 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center text-xl"><i class="fas fa-coins"></i></span>
                            <span>
                                <span class="block font-semibold text-gray-900 group-hover:text-blue-700">Förderprogramme und Fördertöpfe</span>
                                <span class="block mt-1 text-sm text-gray-600">Programme, Budgets, Antragsrunden und Öffnungsstatus verwalten.</span>
                            </span>
                        </a>
                    @endcan

                    @can('antraege.benutzer')
                        <a href="{{ route('antraege.benutzer') }}" class="group bg-gray-50 border border-gray-300 rounded-xl shadow-md hover:bg-white hover:border-green-500 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-green-600 focus:ring-offset-2 transition-all duration-200 p-6 flex items-start space-x-4">
                            <span class="flex-shrink-0 h-12 w-12 rounded-lg bg-green-100 text-green-700 flex items-center justify-center text-xl"><i class="fas fa-users"></i></span>
                            <span>
                                <span class="block font-semibold text-gray-900 group-hover:text-blue-700">Benutzer und Einladungen</span>
                                <span class="block mt-1 text-sm text-gray-600">Einladungslinks erstellen und Rollen zuweisen.</span>
                            </span>
                        </a>
                    @endcan

                    @can('antraege.stellen')
                        <a href="{{ route('antraege.bankkonten') }}" class="group bg-gray-50 border border-gray-300 rounded-xl shadow-md hover:bg-white hover:border-amber-500 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-amber-600 focus:ring-offset-2 transition-all duration-200 p-6 flex items-start space-x-4">
                            <span class="flex-shrink-0 h-12 w-12 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center text-xl"><i class="fas fa-university"></i></span>
                            <span>
                                <span class="block font-semibold text-gray-900 group-hover:text-blue-700">Meine Bankkonten</span>
                                <span class="block mt-1 text-sm text-gray-600">Konten für die Auszahlung von Fördermitteln hinterlegen.</span>
                            </span>
                        </a>
                    @endcan

                    @can('antraege.verwalten')
                        <a href="{{ route('antraege.antragsarten') }}" class="group bg-gray-50 border border-gray-300 rounded-xl shadow-md hover:bg-white hover:border-purple-500 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-purple-600 focus:ring-offset-2 transition-all duration-200 p-6 flex items-start space-x-4">
                            <span class="flex-shrink-0 h-12 w-12 rounded-lg bg-purple-100 text-purple-700 flex items-center justify-center text-xl"><i class="fas fa-list-alt"></i></span>
                            <span>
                                <span class="block font-semibold text-gray-900 group-hover:text-purple-700">Antragsarten und Formulare</span>
                                <span class="block mt-1 text-sm text-gray-600">Formulare, Pflichtfelder, Auswahlmöglichkeiten, Anlagen und Fristen konfigurieren.</span>
                            </span>
                        </a>
                    @endcan

                    <div class="bg-gray-100 border-2 border-dashed border-gray-400 rounded-xl p-6 flex items-start space-x-4">
                        <span class="flex-shrink-0 h-12 w-12 rounded-lg bg-gray-300 text-gray-700 flex items-center justify-center text-xl"><i class="fas fa-file-alt"></i></span>
                        <span>
                            <span class="block font-semibold text-gray-700">Anträge</span>
                            <span class="block mt-1 text-sm text-gray-600">Folgt in einem der nächsten Umsetzungsschritte.</span>
                        </span>
                    </div>
                </div>
            </div>

            <a href="{{ route('dashboard') }}" class="inline-flex items-center text-blue-700 hover:underline">
                <i class="fas fa-arrow-left mr-2"></i> Zurück zum Dashboard
            </a>
        </div>
    </div>
</x-app-layout>
