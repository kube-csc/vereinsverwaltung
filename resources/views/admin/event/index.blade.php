<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Event - Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

              <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                  <div class="mt-8 text-2xl">
                    Events
                  </div>

                  <div class="mt-6 text-gray-500">
                   In diesem Bereich werden die Events des Vereins angelegt und bearbeitet.
                  </div>

              </div>

              <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-2">
                  <div class="p-6">
                      <div class="flex items-center">
                            <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">Event</div>
                      </div>

                      <div class="ml-12">
                          <div class="mt-2 text-sm text-gray-500">

                            <div class="my-4 flex">
                               <a href="{{ route('event.create') }}">
                                <box-icon name='calendar-plus'></box-icon>
                              </a>
                            </div>

                            <div style="text-align: left">
                              <div>
                                  @if (session()->has('success'))
                                  <div class="p-3 bg-green-300 text-green-800 rounded shadow-sm">
                                      {!! session('success') !!}
                                  </div>
                                  @endif
                              </div>

                              @foreach ( $events as $event)
                              <div class="rounded border shadow p-3 my-2 {{$event->id == $event->id ? 'bg-blue-200' : ''}}" onclick="">
                                  <div class="justify-between my-2">
                                      <div>
                                        <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('Event/edit/'.$event->id) }}">
                                            <box-icon name='edit' type='solid'></box-icon>
                                        </a>
                                        <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('/Bericht/alle/'.$event->id) }}">
                                            <box-icon name='image'></box-icon>
                                        </a>
                                        <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('/Bericht/neu/'.$event->id) }}">
                                            <box-icon name='image-add'></box-icon>
                                        </a>
                                        <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('/EventDokumente/'.$event->id) }}">
                                            <box-icon name='file'></box-icon>
                                        </a>
                                        <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('/Event/SocialMedia/'.$event->id) }}">
                                            <box-icon type='solid' name='videos'></box-icon>
                                        </a>
                                        @if(($event->regatta==Null | $event->regatta==0) && env('APP_REGATTA')=="ja") <!-- $event->regatta==0 wird für die alten Daten benötigt -->
                                        <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('/Regatta/aktiv/'.$event->id) }}">
                                            <box-icon name='run'></box-icon>
                                        </a>
                                        @endif
                                        @if($event->mitgliederSicherheitscode)
                                        <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('/Mitgliederbilder/'.$event->id) }}" target="_blank">
                                             <box-icon name='link'></box-icon>
                                        </a>
                                        @endif
                                      </div>
                                  </div>
                                  <div class="justify-between my-2">
                                    <div class="flex">
                                      <p class="font-bold text-lg">{{ $event->ueberschrift }}</p>
                                      <p class="mx-3 py-1 text-xs text-gray-500 font-semibold">{{ $event->updated_at->diffForHumans() }}</p>
                                    </div>
                                    <div class="flex">
                                         von {{ date("d.m.Y", strtotime($event->datumvon)) }} bis {{ date("d.m.Y", strtotime($event->datumbis)) }}
                                    </div>
                                  </div>

                              </div>
                              @endforeach

                              {{ $events->links() }}

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
