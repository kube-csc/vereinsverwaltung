<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Informationsseite - Dashboard') }}
        </h2>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

              <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                  <div class="mt-8 text-2xl">
                     {{ $instruction->ueberschrift }}
                  </div>

                  <div class="mt-6 text-gray-500">
                    @php
                      // ToDo: Beschreibungstext überarbeiten
                    @endphp
                    Bitte gebe die Daten der Informationsseite ein.
                  </div>
              </div>

              <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-2">
                  <div class="p-6">
                      <div class="flex items-center">
                        <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold"> {{ $instruction->ueberschrift }} bearbeitern</div>
                      </div>

                      <div class="ml-12">
                          <div class="mt-2 text-sm text-gray-500">

                              <form autocomplete="off" action="{{ url('Instruction/update/'.$instruction->id) }}" method="post" enctype="multipart/form-data">
                                @csrf
                                @php
                                  // ToDo:  @method('PUT') in Hobby Projekt noch mal erlernen
                                @endphp

                                @if($instruction->ueberschrift <> "Datenschutzerklärung" | $instruction->ueberschrift <> "MENUE_VEREIN" | $instruction->ueberschrift <> "MENUE_VERBAND")
                                <div class="my-4" >
                                  <label for="name">Name der Seite</label>
                                  <input type="text" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('ueberschrift') ? 'bg-red-300' : '' }}"
                                         id="ueberschrift" placeholder="Name der Seite" name="ueberschrift" value="{{ old('ueberschrift') ?? $instruction->ueberschrift }}">
                                  <small class="form-text text-danger">{!! $errors->first('ueberschrift') !!}</small>
                                </div>
                                @else
                                      <input type="hidden" id="ueberschrift" name="ueberschrift" value="{{ old('ueberschrift') ?? $instruction->ueberschrift }}">
                                @endif

                                <div class="my-4" >
                                    <textarea rows="25" cols="200" name="beschreibung" class="w-full rounded border shadow p-2 mr-2 my-2">{!! $instruction->beschreibung !!}</textarea>
                                    <small class="form-text text-danger">{!! $errors->first('beschreibung') !!}</small>
                                </div>
                                <div class="py-2">
                                <button type="submit" class="p-2 bg-blue-500 w-40 rounded shadow text-white">Änderungen speichern</button>
                                </div>
                             </form>
                             <br>
                             <a class="p-2 bg-blue-500 w-40 rounded shadow text-white" href="/Instruction/alle"><i class="fas fa-arrow-circle-up"></i>Zurück</a>
                            </div>
                          </div>

                      </div>
                  </div>

              </div>

            </div>
        </div>
    </div>    @php   // TODO:  Wird der div benötigt?
              @endphp
</x-app-layout>
