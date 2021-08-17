<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Abteilungsdashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

              <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                  <div class="mt-8 text-2xl">
                   Mannschaften
                  </div>

                  <div class="mt-6 text-gray-500">
                   In diesem Bereich werden die Abteilungen des Vereins angelegt und bearbeitet.
                   Für jede Mannschaft gibt es ein Beschreibungsfeld für Informationen.
                   Mit einer eigenen Domain oder Subdomain kann jede v mit einem eigenen Kopfbild im Internet erscheinen. Es kann eine Akzentfarbe für jede Mannschaft gesetzt werden.
                  </div>

              </div>

              <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-2">
                  <div class="p-6">
                      <div class="flex items-center">
                            <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">Mannschaft</div>
                      </div>

                      <div class="ml-12">
                          <div class="mt-2 text-sm text-gray-500">

                            <div style="text-align: left">
                              <div>
                                  @if (session()->has('success'))
                                  <div class="p-3 bg-green-300 text-green-800 rounded shadow-sm">
                                      {!! session('success') !!}
                                  </div>
                                  @endif
                              </div>

                              @foreach ( $sportTeams as $sportTeam )
                              <div class="rounded border shadow p-3 my-2 bg-blue-200" onclick="window.location.replace('edit/{{ $sportTeam->id }}')">
                                  <div class="flex justify-between my-2">
                                    <div class="flex">
                                      <p class="font-bold text-lg">{{ $sportTeam->abteilung }} </p>
                                      <p class="mx-3 py-1 text-xs text-gray-500 font-semibold">{{ $sportTeam->updated_at->diffForHumans() }}</p>
                                         <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('Mannschaft/edit/'.$sportTeam->id) }}">
                                          <box-icon name='edit' type='solid'></box-icon>
                                         </a>
                                          @if($sportTeam['status']==2)
                                            <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('Mannschaft/start/'.$sportTeam->id) }}">
                                             <box-icon name='pin' type='solid'></box-icon>
                                            </a>
                                          @endif
                                          @if($sportTeam['status']==2)
                                            <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('Mannschaft/inaktiv/'.$sportTeam->id) }}">
                                             <box-icon name='show'  type='solid'></box-icon>
                                            </a>
                                          @endif
                                          @if($sportTeam['status']==0)
                                            <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('Mannschaft/aktiv/'.$sportTeam->id) }}">
                                             <box-icon name='hide' type='solid'></box-icon>
                                            </a>
                                            <!-- <i class="fas fa-times text-red-200 hover:text-red-600 cursor-pointer">inaktiv</i> -->
                                          @endif
                                          @if ($sportTeam['event_id']==0)
                                           <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('Mannschaft/softDelete/'.$sportTeam->id) }}">
                                             <box-icon type='solid' name='x-square'></box-icon>
                                           </a>
                                          @endif
                                          @if($sportTeam['status']>0)
                                           <a href="{{ url('Abteilungsevent/neu/'.$sportTeam->id) }}">
                                             <box-icon type='solid' name='calendar-plus'></box-icon>
                                           </a>
                                          @endif
                                      </div>
                                  </div>
                                  @if($sportTeam->bild)
                                   <img src="/storage/header/{{$sportTeam->bild}}" />
                                   <a href="{{ url('Mannschaft/picturedelete/'.$sportTeam->id) }}">
                                     <box-icon name='x'></box-icon>
                                   </a>
                                  @endif
                              </div>
                              @endforeach

                              {{ $sportTeams->links() }}

                              <a class="p-2 bg-blue-500 w-40 rounded shadow text-white" href="/Adminmenu"><i class="fas fa-arrow-circle-up"></i> Zurück</a>

                            </div>
                          </div>

                      </div>
                  </div>

              </div>
            </div>
        </div>
    </div>
</x-app-layout>
