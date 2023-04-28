<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Informationsseiten Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

              <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                  <div class="mt-8 text-2xl">
                      Informationsseiten
                  </div>

                  <div class="mt-6 text-gray-500">
                   In diesem Bereich können Datenschutzerklärung und selbst angelegte Informationsseiten bearbeitet werden.
                  </div>

              </div>

              <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-2">
                  <div class="p-6">
                      <div class="flex items-center">
                            <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">Informationsseiten</div>
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

                              <div class="my-4 flex">
                                  <a href="{{ route('instruction.create') }}"><box-icon name='plus'></box-icon></a>
                              </div>
                             @php
                               $menulevel=0;
                             @endphp
                              @foreach ( $instructions as $instruction )
                                  <div class="rounded border shadow p-3 my-2 {{$instruction->hauptmenu == 2 ? 'bg-blue-300' : 'bg-blue-200'}}">
                                      <div class="justify-between my-2">
                                        <div>
                                            <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('Instruction/edit/'.$instruction->id) }}">
                                                <box-icon name='edit' type='solid'></box-icon>
                                            </a>
                                            @if($instruction['visible']==1)
                                                <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('Instruction/inaktiv/'.$instruction->id) }}">
                                                    <box-icon name='show' type='solid'></box-icon>
                                                </a>
                                            @endif
                                            @if($instruction['visible']==0)
                                                <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('Instruction/aktiv/'.$instruction->id) }}">
                                                    <box-icon name='hide' type='solid'></box-icon>
                                                </a>
                                            @endif
                                            @if($instruction['hauptmenuspalte']>10 | ($instruction['hauptmenuspalte']==10 && $instruction['position']>10) | $instruction['hauptmenu']==2 )
                                                <a href="{{ url('Instruction/maxtop/'.$instruction->id) }}">
                                                    <box-icon name='chevrons-up' ></box-icon>
                                                </a>
                                                <a href="{{ url('Instruction/top/'.$instruction->id) }}">
                                                    <box-icon name='chevron-up'></box-icon>
                                                </a>
                                            @endif
                                            @if(!$loop->last && ($instruction['hauptmenuspalte']>=10 && ($instruction['hauptmenuspalte']<$instructionMaxID) | ($instruction['hauptmenuspalte']==$instructionMaxID && $instruction['hauptmenu']==0) ))
                                                <a href="{{ url('Instruction/down/'.$instruction->id) }}">
                                                    <box-icon name='chevron-down' ></box-icon>
                                                </a>
                                                <a href="{{ url('Instruction/maxdown/'.$instruction->id) }}">
                                                    <box-icon name='chevrons-down' ></box-icon>
                                                </a>
                                            @endif
                                            @if($instruction['hauptmenu']==0)
                                                <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('Instruction/MenuDown/'.$instruction->id) }}">
                                                    <box-icon name='chevron-down'></box-icon>
                                                </a>
                                            @endif
                                            @if($instruction['hauptmenu']==3)
                                                <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('Instruction/MenuDelete/'.$instruction->id) }}">
                                                    <box-icon name='chevron-left'></box-icon>
                                                </a>
                                            @endif
                                            @if($instruction['hauptmenu']==2)
                                                <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('Instruction/MenuMinus/'.$instruction->id) }}">
                                                    <box-icon name='chevrons-left'></box-icon>
                                                </a>
                                            @endif
                                            @if($instruction['hauptmenu']==1)
                                                <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('Instruction/MenuPlus/'.$instruction->id) }}">
                                                    <box-icon name='chevron-right'></box-icon>
                                                </a>
                                            @endif
                                            @if($instruction['hauptmenu']<2 | $instruction['hauptmenu']==3)
                                                <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('Instruction/MenuNeu/'.$instruction->id) }}">
                                                    <box-icon name='chevrons-right'></box-icon>
                                                </a>
                                            @endif

                                        </div>
                                        <div class="flex">
                                          <p class="font-bold text-lg">
                                              @if($instruction->systemmenu==1)
                                                  Systemprogramm<br>
                                              @endif
                                              @if($instruction->hauptmenu==1 | $instruction->hauptmenu==2 )
                                                  @php
                                                      ++$menulevel
                                                  @endphp
                                                  Hauptmenu: {{ $menulevel }}<br>
                                              @endif
                                                  {{ $instruction->ueberschrift }}</p>
                                          <p class="mx-3 py-1 text-xs text-gray-500 font-semibold">{{ $instruction->updated_at->diffForHumans() }}</p>
                                        </div>
                                      </div>
                                  </div>
                              @endforeach
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
