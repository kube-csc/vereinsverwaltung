<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Regattaverwaltung') }}: {{ Session::get('regattaSelectUeberschrift') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

                <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
                    <div class="flex justify-center pt-8 sm:justify-start sm:pt-0 text-xl">
                        Regattaverwaltung
                    </div>

                    <div class="mt-8 bg-white dark:bg-gray-800 overflow-hidden shadow sm:rounded-lg">
                        <div class="grid grid-cols-1 md:grid-cols-2">
                            <div class="p-6">
                                <div class="flex items-center">
                                    <div class="ml-4 text-lg leading-7 font-semibold">
                                        Regatta Tools
                                    </div>
                                </div>

                                <div class="ml-12">
                                    <div class="mt-2 text-gray-600 dark:text-gray-400 text-sm">
                                        <div class="rounded border shadow p-3 my-2 bg-blue-200" onclick="window.location.replace('Regatta/wahl/aktiv')">
                                            <div class="justify-between my-2">
                                                <div class="flex">
                                                    <p class="font-bold text-lg">
                                                        zu einem Event eine Regatta aktivieren
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="ml-12">
                                    <div class="mt-2 text-gray-600 dark:text-gray-400 text-sm">
                                        <div class="rounded border shadow p-3 my-2 bg-blue-200" onclick="window.location.replace('/Regatta/alle')">
                                            <div class="justify-between my-2">
                                                <div class="flex">
                                                    <p class="font-bold text-lg">
                                                        Regatta auswählen
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @if(Session::has('regattaSelectUeberschrift'))
                                    <div class="ml-12">
                                        <div class="mt-2 text-gray-600 dark:text-gray-400 text-sm">
                                            <div class="rounded border shadow p-3 my-2 bg-blue-200" onclick="window.location.replace('/Renneninformation/alle')">
                                                <div class="justify-between my-2">
                                                    <div class="flex">
                                                        <p class="font-bold text-lg">
                                                            Regatta Informationen
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="ml-12">
                                        <div class="mt-2 text-gray-600 dark:text-gray-400 text-sm">
                                            <div class="rounded border shadow p-3 my-2 bg-blue-200" onclick="window.location.replace('/Rennklassen/alle')">
                                                <div class="justify-between my-2">
                                                    <div class="flex">
                                                        <p class="font-bold text-lg">
                                                            Rennklassen
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="ml-12">
                                    <div class="mt-2 text-gray-600 dark:text-gray-400 text-sm">
                                        <div class="rounded border shadow p-3 my-2 bg-blue-200" onclick="window.location.replace('/Rennklassenvorlage/alle')">
                                            <div class="justify-between my-2">
                                                <div class="flex">
                                                    <p class="font-bold text-lg">
                                                        Rennklassen Vorlage
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="ml-12">
                                    <div class="mt-2 text-gray-600 dark:text-gray-400 text-sm">
                                        {{-- FAQ Verwaltung nur anzeigen, wenn eine Regatta ausgewählt ist --}}
                                        @if(Session::has('regattaSelectUeberschrift'))
                                            <div class="rounded border shadow p-3 my-2 bg-blue-200" onclick="window.location.replace('/FAQ/alle')">
                                                <div class="justify-between my-2">
                                                    <div class="flex items-center">
                                                        <box-icon name='help-circle' type='solid'></box-icon>
                                                        <p class="font-bold text-lg ml-2">
                                                            FAQ Verwaltung
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                            </div>

                            <div class="p-6 border-t border-gray-200 dark:border-gray-700 md:border-t-0 md:border-l">
                                <div class="flex items-center">
                                    <div class="ml-4 text-lg leading-7 font-semibold">
                                        Ergebnisse Verwaltung
                                    </div>
                                </div>
                                @if(Session::has('regattaSelectUeberschrift'))
                                    <div class="ml-12">
                                        <div class="mt-2 text-gray-600 dark:text-gray-400 text-sm">
                                            <div class="rounded border shadow p-3 my-2 bg-blue-200" onclick="window.location.replace('/Rennen/Programm')">
                                                <div class="justify-between my-2">
                                                    <div class="flex">
                                                        <p class="font-bold text-lg">
                                                            Fehlende Rennaufstellung
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="ml-12">
                                        <div class="mt-2 text-gray-600 dark:text-gray-400 text-sm">
                                            <div class="rounded border shadow p-3 my-2 bg-blue-200" onclick="window.location.replace('/Rennen/Programm/alle')">
                                                <div class="justify-between my-2">
                                                    <div class="flex">
                                                        <p class="font-bold text-lg">
                                                            Rennaufstellung
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="ml-12">
                                        <div class="mt-2 text-gray-600 dark:text-gray-400 text-sm">
                                            <div class="rounded border shadow p-3 my-2 bg-blue-200" onclick="window.location.replace('/Rennen/Ergebnisse')">
                                                <div class="justify-between my-2">
                                                    <div class="flex">
                                                        <p class="font-bold text-lg">
                                                            Fehlende Ergebnisse
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Neuer Button für Kontrolle Ergebnisse -->
                                    <div class="ml-12">
                                        <div class="mt-2 text-gray-600 dark:text-gray-400 text-sm">
                                            <div class="rounded border shadow p-3 my-2 bg-blue-200" onclick="window.location.replace('/Rennen/Ergebniskontrolle')">
                                                <div class="justify-between my-2">
                                                    <div class="flex">
                                                        <p class="font-bold text-lg">
                                                            Ergebnisse Kontrolle
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="ml-12">
                                        <div class="mt-2 text-gray-600 dark:text-gray-400 text-sm">
                                            <div class="rounded border shadow p-3 my-2 bg-blue-200" onclick="window.location.replace('/Rennen/Ergebnisse/alle')">
                                                <div class="justify-between my-2">
                                                    <div class="flex">
                                                        <p class="font-bold text-lg">
                                                            Ergebnisse
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="p-6 border-t border-gray-200 dark:border-gray-700">
                                <div class="flex items-center">
                                    <div class="ml-4 text-lg leading-7 font-semibold">
                                        Rennen bearbeiten
                                    </div>
                                </div>

                                <div class="ml-12">
                                    <div class="mt-2 text-gray-600 dark:text-gray-400 text-sm">
                                        @if(Session::has('regattaSelectUeberschrift'))
                                            <div class="rounded border shadow p-3 my-2 bg-blue-200" onclick="window.location.replace('/Rennen/neu')">
                                                <div class="justify-between my-2">
                                                    <div class="flex">
                                                        <p class="font-bold text-lg">
                                                            Rennen eingeben
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="ml-12">
                                    <div class="mt-2 text-gray-600 dark:text-gray-400 text-sm">
                                        @if(Session::has('regattaSelectUeberschrift'))
                                            <div class="rounded border shadow p-3 my-2 bg-blue-200" onclick="window.location.replace('/Rennen/alle')">
                                                <div class="justify-between my-2">
                                                    <div class="flex">
                                                        <p class="font-bold text-lg">
                                                            Rennen auswählen
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="p-6 border-t border-gray-200 dark:border-gray-700 md:border-l">
                                <div class="flex items-center">
                                    <div class="ml-4 text-lg leading-7 font-semibold text-gray-900 dark:text-white">Tabellen Ergebnisse Verwaltung</div>
                                </div>

                                @if(Session::has('regattaSelectUeberschrift'))
                                    <div class="ml-12">
                                        <div class="mt-2 text-gray-600 dark:text-gray-400 text-sm">
                                            <div class="rounded border shadow p-3 my-2 bg-blue-200" onclick="window.location.replace('/Tabelle/Ergebnisse')">
                                                <div class="justify-between my-2">
                                                    <div class="flex">
                                                        <p class="font-bold text-lg">
                                                            Fehlende Ergebnisse
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="ml-12">
                                        <div class="mt-2 text-gray-600 dark:text-gray-400 text-sm">
                                            <div class="rounded border shadow p-3 my-2 bg-blue-200" onclick="window.location.replace('/Tabelle/ErgebnisseAlle')">
                                                <div class="justify-between my-2">
                                                    <div class="flex">
                                                        <p class="font-bold text-lg">
                                                            Ergebnisse
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="p-6 border-t border-gray-200 dark:border-gray-700">
                                <div class="flex items-center">
                                    <div class="ml-4 text-lg leading-7 font-semibold">
                                        Tabelle bearbeiten
                                    </div>
                                </div>

                                <div class="ml-12">
                                    <div class="mt-2 text-gray-600 dark:text-gray-400 text-sm">
                                        @if(Session::has('regattaSelectUeberschrift'))
                                            <div class="rounded border shadow p-3 my-2 bg-blue-200" onclick="window.location.replace('/Tabelle/neu')">
                                                <div class="justify-between my-2">
                                                    <div class="flex">
                                                        <p class="font-bold text-lg">
                                                            Tabelle eingeben
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="ml-12">
                                    <div class="mt-2 text-gray-600 dark:text-gray-400 text-sm">
                                        @if(Session::has('regattaSelectUeberschrift'))
                                            <div class="rounded border shadow p-3 my-2 bg-blue-200" onclick="window.location.replace('/Tabelle/alle')">
                                                <div class="justify-between my-2">
                                                    <div class="flex">
                                                        <p class="font-bold text-lg">
                                                            Tabelle auswählen
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="p-6 border-t border-gray-200 dark:border-gray-700 md:border-l">
                                <div class="flex items-center">
                                    <div class="ml-4 text-lg leading-7 font-semibold">
                                        Regattateam bearbeiten
                                    </div>
                                </div>

                                <div class="ml-12">
                                    <div class="mt-2 text-gray-600 dark:text-gray-400 text-sm">
                                        @if(Session::has('regattaSelectUeberschrift'))
                                            <div class="rounded border shadow p-3 my-2 bg-blue-200" onclick="window.location.replace('/Regattateam/neu')">
                                                <div class="justify-between my-2">
                                                    <div class="flex">
                                                        <p class="font-bold text-lg">
                                                            Regattateam eingeben
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="ml-12">
                                    <div class="mt-2 text-gray-600 dark:text-gray-400 text-sm">
                                        @if(Session::has('regattaSelectUeberschrift'))
                                            <div class="rounded border shadow p-3 my-2 bg-blue-200" onclick="window.location.replace('/Regattateam/alle')">
                                                <div class="justify-between my-2">
                                                    <div class="flex">
                                                        <p class="font-bold text-lg">
                                                            Regattateam auswählen
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>


                    <div class="flex justify-center mt-4 sm:items-center sm:justify-between">
                        <div class="text-center text-sm text-gray-500 sm:text-left">

                        </div>

                        <div class="ml-4 text-center text-sm text-gray-500 sm:text-right sm:ml-0">

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
