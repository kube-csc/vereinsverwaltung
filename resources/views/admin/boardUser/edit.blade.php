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
                        <!-- ToDo: Beschreibungstext überarbeiten -->
                        Bitte gebe die Daten des Posten ein.
                    </div>
                </div>

                <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-2">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">Posten bearbeiten</div> <!-- ToDo: Welcher Posten wird bearbeitet -->
                        </div>

                        <div class="ml-12">
                            <div class="mt-2 text-sm text-gray-500">

                                @livewire('board-user-edit', ['boardUserId' => $boardUserId])

                            </div>
                        </div>

                    </div>
                </div>

            </div>

        </div>
    </div>

</x-app-layout>
