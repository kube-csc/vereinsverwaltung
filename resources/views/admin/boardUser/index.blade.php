<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Posten - Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

                <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                    <div class="mt-8 text-2xl">
                        Posten
                    </div>
                    <div class="mt-6 text-gray-500">
                        In diesem Bereich werden das Posten bearbeitet
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
                                        @if (session()->has('success'))
                                            <div class="p-3 bg-green-300 text-green-800 rounded shadow-sm">
                                                {!! session('success') !!}
                                            </div>
                                        @endif
                                    </div>

                                    <div class="my-4 flex">
                                        <a href="{{ route('board.create') }}"><box-icon name='plus'></box-icon></a>
                                    </div>

                                    @php
                                        $boardmax=  $boards->count();

                                        // ToDo: URl Parameter auslesen verbessern
                                        $current_url = url()->full();
                                        $page=explode("=",$current_url);
                                        $count=count($page);
                                    @endphp
                                    @foreach ( $boards as $board )
                                        @php
                                            --$boardmax;
                                        @endphp
                                        <div class="rounded border shadow p-3 my-2 {{$board->id == $boardIdSelecte ? 'bg-blue-300' : 'bg-blue-200'}}"
                                             onclick="window.location.replace('/Posten/{{ $board->id }}{{$count>1 ? '?page='.$page[1] : '' }}')">
                                            <!-- ToDo: URl Parameter auslesen verbessern -->
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
                                                    @if($board['id']==0)
                                                        <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('Team/softDelete/'.$board->id) }}">
                                                            <box-icon type='solid' name='x-square'></box-icon>
                                                        </a>
                                                    @endif
                                                    @if($board['position'] != 10)
                                                        <a href="{{ url('Team/maxtop/'.$board->id) }}">
                                                            <box-icon name='chevrons-up' ></box-icon>
                                                        </a>
                                                        <a href="{{ url('Team/top/'.$board->id) }}">
                                                            <box-icon name='chevron-up'></box-icon>
                                                        </a>
                                                    @endif
                                                    @if($board->id != $boardMaxID)
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
                                                    @if($boardIdSelecte == $board->id && $boardUsers->count() == 0)
                                                        <a href="{{ url('Team/loeschen/'.$board->id) }}">
                                                            <box-icon name='x'></box-icon>
                                                        </a>
                                                    @endif
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
                                                    </p>
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
                            @if ($boardUserName<>'')
                                <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">Posten {{ $boardUserName }}</div>
                            @endif
                        </div>

                        <div>
                            @if (session()->has('successBoardUser'))
                                <div class="p-3 bg-green-300 text-green-800 rounded shadow-sm">
                                    {!! session('successBoardUser') !!}
                                </div>
                            @endif
                        </div>

                        <div class="my-4 flex">
                            <a href="{{ url('Posten/neu/'.$boardIdSelecte) }}"><box-icon name='plus'></box-icon></a>
                        </div>

                        @foreach ( $boardUsers as $boardUser)
                            <div class="rounded border shadow p-3 my-2 bg-blue-200">
                                <div class="justify-between my-2">

                                    <div>
                                        <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('Posten/edit/'.$boardUser->id) }}">
                                            <box-icon name='edit' type='solid'></box-icon>
                                        </a>
                                        @if($boardUser['visible']==1)
                                            <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('Posten/inaktiv/'.$boardUser->id) }}">
                                                <box-icon name='show' ></box-icon>
                                            </a>
                                        @endif
                                        @if($boardUser['visible']==0)
                                            <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('Posten/aktiv/'.$boardUser->id) }}">
                                                <box-icon name='hide' ></box-icon>
                                            </a>
                                        @endif
                                        @if($boardUser['id']==0)
                                            <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('Posten/softDelete/'.$boardUser->id) }}">
                                                <box-icon type='solid' name='x-square'></box-icon>
                                            </a>
                                        @endif
                                        @if($boardUser['position'] != 10)
                                            <a href="{{ url('Posten/maxtop/'.$boardUser->id) }}">
                                                <box-icon name='chevrons-up' ></box-icon>
                                            </a>
                                            <a href="{{ url('Posten/top/'.$boardUser->id) }}">
                                                <box-icon name='chevron-up'></box-icon>
                                            </a>
                                        @endif
                                        @if($boardUser->id != $boardUserMaxID)
                                            <a href="{{ url('Posten/down/'.$boardUser->id) }}">
                                                <box-icon name='chevron-down' ></box-icon>
                                            </a>
                                            <a href="{{ url('Posten/maxdown/'.$boardUser->id) }}">
                                                <box-icon name='chevrons-down' ></box-icon>
                                            </a>
                                        @endif
                                        <a href="{{ url('Posten/zuordnen/'.$boardUser->id) }}">
                                            <box-icon type='solid' name='user-plus'></box-icon>
                                        </a>
                                        @if($boardUser->boardUser_id  == Null)
                                            <a href="{{ url('Posten/loeschen/'.$boardUser->id) }}">
                                                <box-icon name='x'></box-icon>
                                            </a>
                                        @endif
                                    </div>

                                    <div class="flex">
                                        <p class="font-bold text-lg">
                                           @if($boardUser->nummer > 0)
                                             {{ $boardUser->nummer }}.
                                           @endif
                                           @if(isset($boardUser->boardUser_id))
                                             {{ $boardUser->boardUserName->nachname }} {{ $boardUser->boardUserName->vorname }}
                                           @else
                                             noch kein Mitglied zugewiesen
                                           @endif
                                        </p>
                                        <p class="mx-3 py-1 text-xs text-gray-500 font-semibold">{{ $boardUser->updated_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
