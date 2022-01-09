w<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Backlinks - Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

              <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                  <div class="mt-8 text-2xl">
                      weitergeleitete Backlinks
                  </div>

                  <div class="mt-6 text-gray-500">
                   In diesem Bereich werden die Umleitung der Backlinks von vorherrigen Weblayouts angelegt und bearbeitet.
                      @php
                          //ToDO: Texte Verbessern
                      @endphp
                  </div>

              </div>

              <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-2">
                  <div class="p-6">
                      <div class="flex items-center">
                            <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">Backlinks</div>
                      </div>

                      <div class="ml-12">
                          <div class="mt-2 text-sm text-gray-500">

                            <div class="my-4 flex">
                               <a href="{{ route('backlink.create') }}"><box-icon name='plus'></box-icon></a>
                               <a class="p-2 m-2 bg-blue-500 w-40 rounded shadow text-white" href="/Backlink/alle"><i class="fas fa-arrow-circle-up"></i>Backlinks</a>
                               <a class="p-2 m-2 bg-blue-500 w-40 rounded shadow text-white" href="/Backlink/relevant"><i class="fas fa-arrow-circle-up"></i>relevante Backlinks von alten Web-Layout</a>
                            </div>

                            <div style="text-align: left">
                              <div>
                                  @if (session()->has('success'))
                                  <div class="p-3 bg-green-300 text-green-800 rounded shadow-sm">
                                      {!! session('success') !!}
                                  </div>
                                  @endif
                              </div>

                              @foreach ( $backlinks as $backlink )
                              <div class="rounded border shadow p-3 my-2 bg-blue-200">
                                  <div class="justify-between my-2">
                                    <div>
                                        <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('Backlink/edit/'.$backlink->id) }}">
                                            <box-icon name='edit'></box-icon>
                                        </a>
                                        @if($backlink['visible']==1)
                                            <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('Backlink/inaktiv/'.$backlink->id) }}">
                                                <box-icon name='show'></box-icon>
                                            </a>
                                        @endif
                                        @if($backlink['visible']==0)
                                            <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('Backlink/aktiv/'.$backlink->id) }}">
                                                <box-icon name='hide'></box-icon>
                                            </a>
                                        @endif
                                            <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('Backlink/softDelete/'.$backlink->id) }}">
                                                <box-icon name='x'></box-icon>
                                            </a>
                                    </div>

                                      <div class="flex">
                                      <p class="font-bold text-lg">{{ $backlink->backlink }}<br>
                                          {{ $backlink->neueUrl }}<br>
                                          Anzahl der Weiterleitungen: {{ $backlink->weiterleitAnzahl }}<br>
                                          Letzte Weiterleitung: {{ $backlink->weiterleitDatum}}
                                          @php
                                          //ToDo: Datumausgabe verbessern
                                          @endphp
                                      </p>
                                      <p class="mx-3 py-1 text-xs text-gray-500 font-semibold">{{ $backlink->updated_at->diffForHumans() }}</p>
                                    </div>
                                  </div>
                              </div>
                              @endforeach

                              {{ $backlinks->links() }}

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
