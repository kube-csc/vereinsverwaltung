<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Team - Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

              <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                  <div class="mt-8 text-2xl">
                   Team
                  </div>

                  <div class="mt-6 text-gray-500">
                   In diesem Bereich wird das Team bearbeitet.
                  </div>

              </div>

              <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-2">
                  <div class="p-6">
                      <div class="flex items-center">
                            <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">Team</div>
                      </div>

                      <div class="ml-12">
                          <div class="mt-2 text-sm text-gray-500">

                           @php /*
                            @error('messageSportSection')
                              <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                            */
                           @endphp

                            <div style="text-align: left">
                              <div>
                                  @if(session()->has('success'))
                                  <div class="p-3 bg-green-300 text-green-800 rounded shadow-sm">
                                      {!! session('success') !!}
                                  </div>
                                  @else
                                    @if(isset($success))
                                      <div class="p-3 bg-green-300 text-green-800 rounded shadow-sm">
                                          {!! $success !!}
                                      </div>
                                    @endif
                                  @endif
                              </div>

                              <div class="my-4 flex">
                                 <a href="{{ route('board.create') }}"><box-icon name='plus'></box-icon></a>
                              </div>

                              @foreach ( $boards as $board )
                              <div class="rounded border shadow p-3 my-2 {{$board->id == $board->id  ? 'bg-blue-200' : ''}}" onclick="window.location.replace('/Posten/{{ $board->id }}')">
                                  <div class="justify-between my-2">
                                   <div>
                                       <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('Team/edit/'.$board->id) }}">
                                           <box-icon name='edit' type='solid'></box-icon>
                                       </a>
                                       @if($board['visible']==1)
                                           <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('Team/inaktiv/'.$board->id) }}">
                                               <box-icon name='show' ></box-icon>
                                           </a>
                                       @endif
                                       @if($board['visible']==0)
                                           <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('Team/aktiv/'.$board->id) }}">
                                               <box-icon name='hide' ></box-icon>
                                           </a>
                                       @endif
                                       @if ($board['id']==0)
                                           <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('Team/softDelete/'.$board->id) }}">
                                               <box-icon type='solid' name='x-square'></box-icon>
                                           </a>
                                       @endif
                                       @if ($board['position'] != 10)
                                           <a href="{{ url('Team/maxtop/'.$board->id) }}">
                                               <box-icon name='chevrons-up' ></box-icon>
                                           </a>
                                           <a href="{{ url('Team/top/'.$board->id) }}">
                                               <box-icon name='chevron-up'></box-icon>
                                           </a>
                                       @endif
                                       @if ($board->id != $boardMaxID)
                                           <a href="{{ url('Team/down/'.$board->id) }}">
                                               <box-icon name='chevron-down' ></box-icon>
                                           </a>
                                           <a href="{{ url('Team/maxdown/'.$board->id) }}">
                                               <box-icon name='chevrons-down' ></box-icon>
                                           </a>
                                       @endif
                                           <a href="{{ url('Posten/neu/'.$board->id) }}">
                                               <box-icon name='plus'></box-icon>
                                           </a>
                                   </div>
                                   <div class="flex">
                                      <p class="font-bold text-lg">
                                          @php
                                              $sportSections= DB::table('sport_sections')
                                                ->where('id' , $board->sportSection_id)
                                                ->get();
                                          @endphp
                                          @foreach($sportSections as $sportSection)
                                              {{ $sportSection->abteilung }}
                                          @endforeach
                                          <br>
                                          {{ $board->postenMaenlich }}<br>
                                          {{ $board->postenWeiblich }}
                                      <p class="mx-3 py-1 text-xs text-gray-500 font-semibold">
                                          {{ $board->updated_at->diffForHumans() }}
                                      </p>
                                   </div>
                                  </div>
                               </div>
                              @endforeach

                              {{ $boards->links() }}

                             <br>
                             <a class="p-2 bg-blue-500 w-40 rounded shadow text-white" href="/Adminmenu"><i class="fas fa-arrow-circle-up"></i>Zurück</a>
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
