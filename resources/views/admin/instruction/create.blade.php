<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Informationsseite - Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

              <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                  <div class="mt-8 text-2xl">
                     Neue Informationsseite
                  </div>

                  <div class="mt-6 text-gray-500">
                    Bitte gib eine neue Informationsseite ein.
                    Nach dem Anlegen wirst du direkt zur Bearbeitung weitergeleitet, um Inhalte (Seiteninhalt, Headerbild usw.) zu pflegen.
                    Die Seite wird zunächst als Menüpunkt am Ende des Frontend-Menüs eingeordnet (Menüspalte = max + 10, Position = 10).
                  </div>

              </div>

              <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-2">
                  <div class="p-6">
                      <div class="flex items-center">
                            <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">Neue Informationsseite</div>
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

                              <form class="my-4" autocomplete="off" action="{{ route('instruction.store') }}" method="post">
                                @csrf
                                <div>
                                    <label for="name">Informationsseite:</label>
                                    <input type="text" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('ueberschrift') ? 'bg-red-300' : '' }}"
                                    id="ueberschrift" placeholder="Name der Informationsseite" name="ueberschrift" value="{{ old('ueberschrift') }}">
                                    <small class="form-text text-danger">{!! $errors->first('ueberschrift') !!}</small>
                                </div>

                                <div class="mt-4">
                                    <label for="accentColor">Akzentfarbe (optional)</label>
                                    <div class="text-xs text-gray-600">Hex-Farbe, z.B. <span class="font-mono">#0ea5e9</span>. Leer lassen = Standard.</div>
                                    <div class="flex items-center gap-4">
                                        <input
                                            type="color"
                                            id="accentColorPicker"
                                            value="#000000"
                                            class="h-10 w-16 border rounded shadow"
                                            title="Akzentfarbe auswählen"
                                        >

                                        <input type="text" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('accentColor') ? 'bg-red-300' : '' }}"
                                               id="accentColor" placeholder="#RRGGBB" name="accentColor" value="{{ old('accentColor') }}" inputmode="text">
                                    </div>
                                    <small class="form-text text-danger">{!! $errors->first('accentColor') !!}</small>

                                    <script>
                                        (function () {
                                            var picker = document.getElementById('accentColorPicker');
                                            var text = document.getElementById('accentColor');
                                            if (!picker || !text) return;
                                            picker.addEventListener('input', function () {
                                                text.value = picker.value;
                                            });
                                        })();
                                    </script>
                                </div>
                                <div class="py-2">
                                <button type="submit" class="p-2 bg-blue-500 w-40 rounded shadow text-white">anlegen</button>
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
    </div>
</x-app-layout>
