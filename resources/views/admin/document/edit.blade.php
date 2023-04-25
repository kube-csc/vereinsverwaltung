<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dokumentenverwaltung - Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

               <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                  <div class="mt-8 text-2xl">
                     {{ $document->dokumentenName }}
                  </div>

                  <div class="mt-6 text-gray-500">
                    @php
                      // ToDo: Beschreibungstext überarbeiten
                    @endphp
                      Dokumentenverwaltung
                  </div>
               </div>

               <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-2">
                  <div class="p-6">
                      <div class="flex items-center">
                        <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">{{ $document->dokumentenName }} bearbeitern</div>
                      </div>

                      <div class="ml-12">
                          <div class="mt-2 text-sm text-gray-500">

                              <div>
                                  @if (session()->has('success'))
                                      <div class="p-3 bg-green-300 text-green-800 rounded shadow-sm">
                                          {!! session('success') !!}
                                      </div>
                                  @endif
                              </div>

                              <form autocomplete="off" action="{{ url('Dokumente/update/'.$document->id) }}" method="post" enctype="multipart/form-data">
                                @csrf
                                @php
                                  // ToDo:  @method('PUT') in Hobby Projekt noch mal erlernen
                                @endphp
                                <div class="my-4" >
                                  <label for="Dokumentenname">Dokumenten Name:</label>
                                  <input type="text" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('dokumentenName') ? 'bg-red-300' : '' }}"
                                         id="dokumentenName" placeholder="Dokumentenname" name="dokumentenName" value="{{ old('dokumentenName') ?? $document->dokumentenName  }}">
                                  <small class="form-text text-danger">{!! $errors->first('dokumentenName') !!}</small>
                                </div>
                                <div class="my-4" >
                                  <label for="name">Dokument:</label>

                                    @if(isset($document->dokumentenFile))
                                        <div class="flex ml-2">
                                            <div class="flex-initial"><a href="/storage/document/{{$document->dokumentenFile}}" target="_blank">{{$document->dokumentenFile}}</a></div>
                                            <div class="flex-initial ml-2 fas fa-times text-red-600 hover:text-red-00 cursor-pointer">
                                                <a href="/Dokumente/geloescht/{{ $document->id }}">x</a>
                                            </div>
                                        </div>
                                    @endif

                                  <input type="file" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('documentFile') ? 'bg-red-300' : '' }}"
                                         id="documentFile" name="documentFile" value="">
                                  <small class="form-text text-danger">{!! $errors->first('documentFile') !!}</small>
                                </div>

                                <div class="my-4" >
                                  <label for="footerStatus">Dokument angezeigt im Footer:</label><br>
                                  <input type="checkbox" class="border rounded shadow p-2 mr-2 my-2 {{ $errors->has('footerStatus') ? 'bg-red-300' : '' }}"
                                         id="footerStatus" name="footerStatus" value="1"
                                         @if(old('footerStatus')==1 | $document->footerStatus==1)
                                           checked
                                         @endif
                                  >
                                  <small class="form-text text-danger">{!! $errors->first('footerStatus') !!}</small>
                                </div>

                                <div class="my-4" >
                                  <label for="instruction_id">Informationsseiten:</label><br>
                                  <select name="instruction_id" class="w-full border rounded shadow p-2 mr-2 my-2">
                                      <!-- ToDo: Verbesserung Old Wert behalten bei Valiedierungsfehler -->
                                      <option value=""
                                              @if ($document->instruction_id == NULL)
                                              selected
                                          @endif
                                      >keine Zuordnung
                                      </option>
                                          @foreach ($instructions as $instruction)
                                           <option value="{{ $instruction->id }}"
                                              @if ($document->instruction_id == $instruction->id)
                                                  selected
                                              @endif
                                           >{{ $instruction->ueberschrift }}</option>
                                          @endforeach
                                  </select>
                                </div>

                                <div class="my-4" >
                                    <label for="name">{{ env('MENUE_ABTEILUNG') }}
                                        @if(env('MENUE_MANNSCHAFTEN')<>"nein")
                                            / {{ env('MENUE_MANNSCHAFTEN') }}
                                        @endif
                                        :
                                    </label>
                                    <br>
                                  <select name="sportSection_id" class="w-full border rounded shadow p-2 mr-2 my-2">
                                      <!-- ToDo: Verbesserung Old Wert behalten bei Valiedierungsfehler -->
                                      <option value=""
                                              @if ($document->sportSection_id == NULL)
                                              selected
                                          @endif
                                      >keine Zuordnung
                                      </option>
                                      <optgroup label="{{ env('MENUE_ABTEILUNG') }}:">
                                          @php
                                              $firsttime = 0;
                                          @endphp

                                          @foreach ($sportSections as $sportSection)
                                              @if ($sportSection->sportSection_id > 0 && $firsttime == 0)
                                                  @php
                                                      $firsttime = 1;
                                                  @endphp
                                      </optgroup>
                                      <optgroup label="{{ env('MENUE_MANNSCHAFTEN') }}:">
                                              @endif
                                          <option value="{{ $sportSection->id }}"
                                              @if ($document->sportSection_id == $sportSection->id)
                                                  selected
                                              @endif
                                          >{{ $sportSection->abteilung }}</option>
                                          @endforeach

                                      </optgroup>
                                  </select>
                                </div>

                                <div class="py-2">
                                   <button type="submit" class="p-2 bg-blue-500 w-40 rounded shadow text-white">Änderungen speichern</button>
                                </div>

                             </form>
                             <br>
                             <a class="p-2 bg-blue-500 w-40 rounded shadow text-white" href="/Dokumente/alle"><i class="fas fa-arrow-circle-up"></i>Zurück</a>

                          </div>
                      </div>
                  </div>

               </div>

            </div>
        </div>
    </div>

</x-app-layout>
