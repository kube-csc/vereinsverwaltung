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
                     {{ $instruction->ueberschrift }}
                  </div>

                  <div class="mt-6 text-gray-500">
                    Bitte gib die Daten der Informationsseite ein und speichere anschließend deine Änderungen.
                    Nach dem Speichern werden die Inhalte im Frontend entsprechend aktualisiert.
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
                                @if($instruction->ueberschrift !== 'Datenschutzerklärung' && $instruction->ueberschrift !== 'MENUE_VEREIN' && $instruction->ueberschrift !== 'MENUE_VERBAND')
                                <div class="my-4" >
                                  <label for="name">Name der Seite</label>
                                  <input type="text" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('ueberschrift') ? 'bg-red-300' : '' }}"
                                         id="ueberschrift" placeholder="Name der Seite" name="ueberschrift" value="{{ old('ueberschrift') ?? $instruction->ueberschrift }}">
                                  <small class="form-text text-danger">{!! $errors->first('ueberschrift') !!}</small>
                                </div>
                                @else
                                      <input type="hidden" id="ueberschrift" name="ueberschrift" value="{{ old('ueberschrift') ?? $instruction->ueberschrift }}">
                                @endif

                                <div class="my-4">
                                     <label class="block font-semibold" for="headerBild">Headerbild (optional)</label>

                                     @if(!empty($instruction->headerBild))
                                         <div class="my-2">
                                             <div class="text-xs text-gray-600">Aktuelles Headerbild:</div>
                                             <img src="{{ asset('storage/'.$instruction->headerBild) }}" alt="Headerbild" style="max-height: 180px;" class="rounded border">
                                         </div>

                                         <label class="inline-flex items-center mt-2">
                                             <input type="checkbox" name="headerBild_remove" value="1" class="mr-2">
                                             <span class="text-sm text-gray-700">Headerbild entfernen</span>
                                         </label>
                                     @endif

                                     <input
                                         type="file"
                                         id="headerBild"
                                         name="headerBild"
                                         accept="image/*"
                                         class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('headerBild') ? 'bg-red-300' : '' }}"
                                     >
                                     <small class="form-text text-danger">{!! $errors->first('headerBild') !!}</small>

                                     <div class="my-4">
                                         <label class="block font-semibold" for="headerTitel">Header-Titel (optional)</label>
                                         <input
                                             type="text"
                                             id="headerTitel"
                                             name="headerTitel"
                                             placeholder="Titel im Header (überschreibt die Überschrift)"
                                             value="{{ old('headerTitel') ?? $instruction->headerTitel }}"
                                             class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('headerTitel') ? 'bg-red-300' : '' }}"
                                         >
                                         <small class="form-text text-danger">{!! $errors->first('headerTitel') !!}</small>
                                     </div>

                                     <div class="my-4">
                                         <label class="block font-semibold" for="headerSlogen">Header-Slogen (optional)</label>
                                         <input
                                             type="text"
                                             id="headerSlogen"
                                             name="headerSlogen"
                                             placeholder="Untertitel im Header"
                                             value="{{ old('headerSlogen') ?? $instruction->headerSlogen }}"
                                             class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('headerSlogen') ? 'bg-red-300' : '' }}"
                                         >
                                         <small class="form-text text-danger">{!! $errors->first('headerSlogen') !!}</small>
                                     </div>

                                     <div class="my-4">
                                         <label class="block font-semibold" for="accentColor">Akzentfarbe (optional)</label>
                                         <div class="text-xs text-gray-600">Hex-Farbe, z.B. <span class="font-mono">#0ea5e9</span>. Leer lassen = Standardfarbe.</div>

                                         @php
                                             // HTML <input type="color"> erwartet i.d.R. #RRGGBB.
                                             // Wenn eine Kurzform (#RGB) gespeichert wurde, erweitern wir sie für die Picker-Initialisierung.
                                             $accentColorText = old('accentColor') ?? $instruction->accentColor;
                                             $accentColorPicker = '#000000';
                                             if (!empty($accentColorText) && preg_match('/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $accentColorText)) {
                                                 if (strlen($accentColorText) === 4) {
                                                     $accentColorPicker = '#' . $accentColorText[1] . $accentColorText[1] . $accentColorText[2] . $accentColorText[2] . $accentColorText[3] . $accentColorText[3];
                                                 } else {
                                                     $accentColorPicker = $accentColorText;
                                                 }
                                             }
                                         @endphp

                                         <div class="flex items-center gap-4">
                                             <input
                                                 type="color"
                                                 id="accentColorPicker"
                                                 value="{{ $accentColorPicker }}"
                                                 class="h-10 w-16 border rounded shadow"
                                                 title="Akzentfarbe auswählen"
                                             >

                                             <input
                                                 type="text"
                                                 id="accentColor"
                                                 name="accentColor"
                                                 placeholder="#RRGGBB"
                                                 value="{{ $accentColorText }}"
                                                 class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('accentColor') ? 'bg-red-300' : '' }}"
                                             >

                                             @if(!empty($instruction->accentColor))
                                                 <div class="flex items-center">
                                                     <div class="w-8 h-8 rounded border" style="background: {{ $instruction->accentColor }}" title="{{ $instruction->accentColor }}"></div>
                                                 </div>
                                             @endif
                                         </div>

                                         <small class="form-text text-danger">{!! $errors->first('accentColor') !!}</small>

                                         @if(!empty($instruction->accentColor))
                                             <label class="inline-flex items-center mt-2">
                                                 <input type="checkbox" name="accentColor_reset" value="1" class="mr-2">
                                                 <span class="text-sm text-gray-700">Akzentfarbe zurücksetzen (Standard verwenden)</span>
                                             </label>
                                         @endif

                                         <script>
                                             (function () {
                                                 var picker = document.getElementById('accentColorPicker');
                                                 var text = document.getElementById('accentColor');
                                                 var reset = document.querySelector('input[name="accentColor_reset"]');
                                                 if (!picker || !text) return;

                                                 picker.addEventListener('input', function () {
                                                     text.value = picker.value;
                                                     if (reset) reset.checked = false;
                                                 });
                                             })();
                                         </script>
                                     </div>
                                 </div>

                                <div class="my-4" >
                                    <label class="block font-semibold" for="headerSlogen">Seiteninhalt:</label>
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
    @php   // TODO:  Wird der div benötigt?
    @endphp
</x-app-layout>
