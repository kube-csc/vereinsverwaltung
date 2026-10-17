<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Antragsverwaltung
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800">
                    Willkommen in der Antragsverwaltung
                </h3>
                <p class="mt-2 text-gray-600">
                    Dieser Bereich wird schrittweise aufgebaut. Die Verwaltung von Förder- und Vereinsanträgen folgt in späteren Umsetzungsschritten.
                </p>
                @can('antraege.benutzer')
                    <a href="{{ route('antraege.benutzer') }}" class="inline-block mt-6 mr-6 text-blue-700 underline">
                        Benutzer und Einladungen
                    </a>
                @endcan
                <a href="{{ route('dashboard') }}" class="inline-block mt-6 text-blue-700 underline">
                    Zurück zum Dashboard
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
