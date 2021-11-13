<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Abteilung - Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

              <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                  <div class="mt-8 text-2xl">
                   Abteilung
                  </div>

                  <div class="mt-6 text-gray-500">
                   In diesem Bereich werden die Abteilungen des Vereins angelegt und bearbeitet.
                   Für jede Abteilung gibt es ein Beschreibungsfeld für Informationen.
                   Mit einer eigenen Domain oder Subdomain kann jede Abteilung mit einem eigenen Kopfbild im Internet erscheinen. Es kann eine Akzentfarbe für jede Abteilung gesetzt werden.
                  </div>

              </div>

              <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-2">
                  <div class="p-6">
                      <div class="flex items-center">
                            <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">Abteilung</div>
                      </div>

                      <div class="ml-12">
                          <div class="mt-2 text-sm text-gray-500">

                           @php /*
                            @error('messageSportSection')
                              <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                            */
                           @endphp

                            <div class="my-4 flex">
                               <a href="{{ route('sportSection.create') }}"><box-icon name='plus'></box-icon></a>
                            </div>

                            <div style="text-align: left">
                              <div>
                                  @if (session()->has('success'))
                                  <div class="p-3 bg-green-300 text-green-800 rounded shadow-sm">
                                      {!! session('success') !!}
                                  </div>
                                  @endif
                              </div>

                              @foreach ( $sportSections as $sportSection )
                              <div class="rounded border shadow p-3 my-2 {{$sportSection->id == $sportSection->id ? 'bg-blue-200' : ''}}" onclick="window.location.replace('sportSectionSportTeam/{{ $sportSection->id }}')">
                                  <div class="justify-between my-2">
                                   <div>
                                       <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('Abteilung/edit/'.$sportSection->id) }}">
                                           <box-icon name='edit' </box-icon>
                                       </a>
                                       @if($sportSection['status']==2)
                                           <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('Abteilung/start/'.$sportSection->id) }}">
                                               <box-icon name='pin'</box-icon>
                                           </a>
                                       @endif
                                       @if($sportSection['status']==2)
                                           <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('Abteilung/inaktiv/'.$sportSection->id) }}">
                                               <box-icon name='show'></box-icon>
                                           </a>
                                       @endif
                                       @if($sportSection['status']==0)
                                           <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('Abteilung/aktiv/'.$sportSection->id) }}">
                                               <box-icon name='hide' ></box-icon>
                                           </a>
                                           <!-- <i class="fas fa-times text-red-200 hover:text-red-600 cursor-pointer">inaktiv</i> -->
                                       @endif
                                       @if ($sportSection['event_id']==0)
                                           <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('Abteilung/softDelete/'.$sportSection->id) }}">
                                               <box-icon name='x-square'></box-icon>
                                           </a>
                                       @endif
                                       @if ($sportSection['status']>0)
                                           <a href="{{ url('Mannschaft/neu/'.$sportSection->id) }}">
                                               <box-icon name='user-plus'></box-icon>
                                           </a>
                                           <a href="{{ url('Abteilungsevent/neu/'.$sportSection->id) }}">
                                               <box-icon name='calendar-plus'></box-icon>
                                           </a>
                                       @endif
                                   </div>

                                    <div class="flex">
                                      <p class="font-bold text-lg">{{ $sportSection->abteilung }}</p>
                                      <p class="mx-3 py-1 text-xs text-gray-500 font-semibold">{{ $sportSection->updated_at->diffForHumans() }}</p>
                                    </div>
                                  </div>
                                  @if($sportSection->bild)
                                   <img src="/storage/header/{{$sportSection->bild}}" />
                                   <a href="{{ url('Abteilung/picturedelete/'.$sportSection->id) }}">
                                     <box-icon name='x'></box-icon>
                                   </a>
                                  @endif
                              </div>
                              @endforeach

                              {{ $sportSections->links() }}

                             <br>
                             <a class="p-2 bg-blue-500 w-40 rounded shadow text-white" href="/Adminmenu"><i class="fas fa-arrow-circle-up"></i> Zurück</a>
                            </div>
                          </div>

                      </div>
                  </div>

                  <div class="p-6 border-t border-gray-200 md:border-t-0 md:border-l">
                      <div class="flex items-center">

                       <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">

                         </div>

                      </div>

                  </div>

              </div>

            </div>
        </div>
    </div>
</x-app-layout>
