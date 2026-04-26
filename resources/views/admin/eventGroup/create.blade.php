<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Event Gruppen - Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

                <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                    <div class="mt-8 text-2xl">
                        Eventgruppen
                    </div>

                    <div class="mt-6 text-gray-500">
                        Bitte gib die Daten der neuen Eventgruppe ein.
                    </div>

                </div>

                <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-2">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">Neue Eventgruppe anlegen</div>
                        </div>

                        <div class="ml-12">
                            <div class="mt-2 text-sm text-gray-500">

                                @error('messageSportSection')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror

                                <div style="text-align: left">
                                    <div>
                                        @if (session()->has('message'))
                                            <div class="p-3 bg-green-300 text-green-800 rounded shadow-sm">
                                                {{ session('message') }}
                                            </div>
                                        @endif
                                    </div>

                                    <form class="my-4" autocomplete="off" action="{{ route('eventGroup.store') }}" method="post" enctype="multipart/form-data">
                                        @csrf
                                        <div>
                                            <label for="termingruppe">Name der Eventgruppe:</label>
                                            <input type="text" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('termingruppe') ? 'bg-red-300' : '' }}"
                                                   id="termingruppe" placeholder="Event Gruppe" name="termingruppe" value="{{ old('termingruppe') }}">
                                            <small class="form-text text-danger">{!! $errors->first('termingruppe') !!}</small>
                                        </div>

                                        <div>
                                            <label for="headerTitel">Header-Titel:</label>
                                            <input type="text" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('headerTitel') ? 'bg-red-300' : '' }}"
                                                   id="headerTitel" placeholder="Header Titel" name="headerTitel" value="{{ old('headerTitel') }}">
                                            <small class="form-text text-danger">{!! $errors->first('headerTitel') !!}</small>
                                        </div>

                                        <div>
                                            <label for="headerSlogen">Header-Slogan:</label>
                                            <input type="text" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('headerSlogen') ? 'bg-red-300' : '' }}"
                                                   id="headerSlogen" placeholder="Header Slogen" name="headerSlogen" value="{{ old('headerSlogen') }}">
                                            <small class="form-text text-danger">{!! $errors->first('headerSlogen') !!}</small>
                                        </div>

                                        <div>
                                            <label class="block font-semibold" for="headerBild">Headerbild (optional)</label>
                                            <input
                                                type="file"
                                                id="headerBild"
                                                name="headerBild"
                                                accept="image/*"
                                                class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('headerBild') ? 'bg-red-300' : '' }}"
                                            >
                                            <small class="form-text text-danger">{!! $errors->first('headerBild') !!}</small>
                                        </div>

                                        <div>
                                            <label for="domain">Domain:</label>
                                            <input type="text" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('domain') ? 'bg-red-300' : '' }}"
                                                   id="domain" placeholder="Domain" name="domain" value="{{ old('domain') }}">
                                            <small class="form-text text-danger">{!! $errors->first('domain') !!}</small>
                                        </div>
                                        <div class="py-2">
                                            <button type="submit" class="p-2 bg-blue-500 w-40 rounded shadow text-white">Eventgruppe anlegen</button>
                                        </div>
                                    </form>
                                    <br>
                                    <a class="p-2 bg-blue-500 w-40 rounded shadow text-white" href="/Eventgruppe/alle"><i class="fas fa-arrow-circle-up"></i>Zurück zur Übersicht</a>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</x-app-layout>
