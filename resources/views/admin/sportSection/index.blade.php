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
                              @php
                                /*
                                onclick="window.location.replace('edit/{{ $sportSection->id
                                */
                              @endphp
                                  <div class="flex justify-between my-2">
                                    <div class="flex">
                                      <p class="font-bold text-lg">{{ $sportSection->abteilung }}</p>
                                      <p class="mx-3 py-1 text-xs text-gray-500 font-semibold">{{ $sportSection->updated_at->diffForHumans() }}</p>
                                         <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('Abteilung/edit/'.$sportSection->id) }}">
                                          <box-icon name='edit' type='solid'></box-icon>
                                         </a>
                                          @if($sportSection['status']==2)
                                            <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('Abteilung/start/'.$sportSection->id) }}">
                                             <box-icon name='pin' type='solid'></box-icon>
                                            </a>
                                          @endif
                                          @if($sportSection['status']==2)
                                            <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('Abteilung/inaktiv/'.$sportSection->id) }}">
                                             <box-icon name='show'  type='solid'></box-icon>
                                            </a>
                                          @endif
                                          @if($sportSection['status']==0)
                                            <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('Abteilung/aktiv/'.$sportSection->id) }}">
                                             <box-icon name='hide' type='solid'></box-icon>
                                            </a>
                                            <!-- <i class="fas fa-times text-red-200 hover:text-red-600 cursor-pointer">inaktiv</i> -->
                                          @endif
                                          @if ($sportSection['event_id']==0)
                                           <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('Abteilung/softDelete/'.$sportSection->id) }}">
                                             <box-icon type='solid' name='x-square'></box-icon>
                                           </a>
                                          @endif

                                          @if ($sportSection['status']>0)
                                           <a href="{{ url('Mannschaft/neu/'.$sportSection->id) }}">
                                             <box-icon type='solid' name='user-plus'></box-icon>
                                           </a>
                                          @endif
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

                             <a class="p-2 bg-blue-500 w-40 rounded shadow text-white" href="/Adminmenu"><i class="fas fa-arrow-circle-up"></i> Zurück</a>
                            </div>
                          </div>

                      </div>
                  </div>

                  <div class="p-6 border-t border-gray-200 md:border-t-0 md:border-l">
                      <div class="flex items-center">
                      @if (session('SportSectionName'))
                       <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">Mannschaften der Abteilung {{ session('SportSectionName') }}
                       <p>Hier werden die Mannschaften der Abteilung gelistet.</p>
                         </div>
                      @endif
                      </div>

                        @foreach ( $sportTeams as $sportSection )
                        <div class="rounded border shadow p-3 my-2 bg-blue-200" onclick="window.location.replace('sportSectionSportTeam/{{ $sportSection->id }}')">
                        @php
                          /*
                          onclick="window.location.replace('edit/{{ $sportSection->id
                          */
                        @endphp
                            <div class="flex justify-between my-2">
                              <div class="flex">
                                <p class="font-bold text-lg">{{ $sportSection->abteilung }} </p>
                                <p class="mx-3 py-1 text-xs text-gray-500 font-semibold">{{ $sportSection->updated_at->diffForHumans() }}</p>
                                   <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('Abteilung/edit/'.$sportSection->id) }}">
                                    <box-icon name='edit' type='solid'></box-icon>
                                   </a>
                                    @if($sportSection['status']==2)
                                      <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('Abteilung/start/'.$sportSection->id) }}">
                                       <box-icon name='pin' type='solid'></box-icon>
                                      </a>
                                    @endif
                                    @if($sportSection['status']==2)
                                      <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('Abteilung/inaktiv/'.$sportSection->id) }}">
                                       <box-icon name='show'  type='solid'></box-icon>
                                      </a>
                                    @endif
                                    @if($sportSection['status']==0)
                                      <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('Abteilung/aktiv/'.$sportSection->id) }}">
                                       <box-icon name='hide' type='solid'></box-icon>
                                      </a>
                                      <!-- <i class="fas fa-times text-red-200 hover:text-red-600 cursor-pointer">inaktiv</i> -->
                                    @endif
                                    @if ($sportSection['event_id']==0)
                                     <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('Abteilung/softDelete/'.$sportSection->id) }}">
                                       <box-icon type='solid' name='x-square'></box-icon>
                                     </a>
                                    @endif

                                    @if ($sportSection['status']>0)
                                     <a href="{{ url('Mannschaft/neu/'.$sportSection->id) }}">
                                       <box-icon type='solid' name='user-plus'></box-icon>
                                     </a>
                                    @endif
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

                  </div>
@php /*


<div class="p-6 border-t border-gray-200 md:border-t-0 md:border-l">
    <div class="flex items-center">
        <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">Mannschaften der Abteilung {{ session('SportSectionName') }}</div>
    </div>

    <div class="ml-12">
        <div class="mt-2 text-sm text-gray-500">
             test 55
        </div>

        <a href="https://laracasts.com">
            <div class="mt-3 flex items-center text-sm font-semibold text-indigo-700">
                    <div>Start watching Laracasts</div>

                    <div class="ml-1 text-indigo-500">
                        <svg viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                    </div>
            </div>
        </a>
    </div>


</div>



                  <div class="p-6 border-t border-gray-200">
                      <div class="flex items-center">
                          <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" class="w-8 h-8 text-gray-400"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                          <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold"><a href="https://tailwindcss.com/">Mannschaft</a></div>
                      </div>

                      <div class="ml-12">
                          <div class="mt-2 text-sm text-gray-500">
                              Laravel Jetstream is built with Tailwind, an amazing utility first CSS framework that doesn't get in your way. You'll be amazed how easily you can build and maintain fresh, modern designs with this wonderful framework at your fingertips.
                          </div>
                      </div>
                  </div>

                  <div class="p-6 border-t border-gray-200 md:border-l">
                      <div class="flex items-center">
                          <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" class="w-8 h-8 text-gray-400"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                          <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">Authentication</div>
                      </div>

                      <div class="ml-12">
                          <div class="mt-2 text-sm text-gray-500">
                              Authentication and registration views are included with Laravel Jetstream, as well as support for user email verification and resetting forgotten passwords. So, you're free to get started what matters most: building your application.
                          </div>
                      </div>
                  </div>
                */
                @endphp
              </div>




            </div>
        </div>
    </div>
</x-app-layout>
