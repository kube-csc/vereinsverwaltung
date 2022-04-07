<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Posten - Dashboard') }}
        </h2>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

                <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                    <div class="mt-8 text-2xl">
                        Posten:
                    </div>

                    <div class="mt-6 text-gray-500">
                        Bitte gebe die Daten des Postens ein.<br>
                        Wenn ein Porträt in der Mitgliederverwaltung vorhanden ist, wird dieses bevorzugt verwendet.
                        <!-- ToDo: Anleitung bearbeiten -->
                    </div>
                </div>
                                @livewire('board-user-match', ['boardUserId' => $boardUserId])
            </div>
        </div>
    </div>
</x-app-layout>
