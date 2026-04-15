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
                               $hasPreviousDropdownContext = false;

                               // Für Lookahead ("nächster Menüpunkt") brauchen wir konsistente numerische Indizes.
                               $instructions = $instructions->values();
                             @endphp
                               @foreach ( $instructions as $instruction )
                                   @php
                                     $isChild = ((int)$instruction->hauptmenu === 3);

                                     // UI-Regel: Bei HM=3 dürfen die Down-Pfeile nur erscheinen, wenn der nächste
                                     // Menüpunkt wieder HM=3 ist (sonst ist es das letzte Dropdown-Item im Block).
                                     $nextInstruction = $instructions->get($loop->index + 1);
                                     $nextIsChildSameBlock = $nextInstruction
                                         && ((int)$nextInstruction->hauptmenu === 3)
                                         && ((int)$nextInstruction->hauptmenuspalte === (int)$instruction->hauptmenuspalte);

                                     $allowDownArrows = !$loop->last && (!$isChild || $nextIsChildSameBlock);
                                   @endphp
                                   <div class="rounded border shadow p-3 my-2 {{$instruction->hauptmenu == 2 ? 'bg-blue-300' : 'bg-blue-200'}} {{$isChild ? 'ml-6 border-l-4 border-blue-500' : ''}}">
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
                                            @if($instruction['hauptmenuspalte']>10 && $instruction['hauptmenu'] < 3  || ($instruction['hauptmenu'] == 3 && $instruction['position']>10))
                                                <!--|| ($instruction['hauptmenuspalte']==10 && $instruction['position']>10) || $instruction['hauptmenu']==2 ) -->
                                                <a href="{{ url('Instruction/maxtop/'.$instruction->id) }}">
                                                    <box-icon name='chevrons-up' ></box-icon>
                                                </a>
                                            @endif
                                            @if( $instruction['hauptmenuspalte']>10 && $instruction['hauptmenu'] < 3  || $instruction['position']>10 && $instruction['hauptmenu']==3 )
                                                <a href="{{ url('Instruction/top/'.$instruction->id) }}">
                                                    <box-icon name='chevron-up'></box-icon>
                                                </a>
                                            @endif
                                            @if($allowDownArrows && ($instruction['hauptmenuspalte']>=10 && (($instruction['hauptmenuspalte']<$instructionMaxID) || ($instruction['hauptmenuspalte']==$instructionMaxID && $instruction['hauptmenu']==0)) ))
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
                                            @if($instruction['hauptmenu']==1)
                                                <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('Instruction/keinMenu/'.$instruction->id) }}" title="Container auflösen">
                                                    <box-icon name='chevron-left'></box-icon>
                                                </a>
                                            @endif
                                            @if($instruction['hauptmenu']==3)
                                                <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('Instruction/MenuDelete/'.$instruction->id) }}">
                                                    <box-icon name='chevron-left'></box-icon>
                                                </a>
                                            @endif
                                            @if($instruction['hauptmenu']==2)
                                                @php
                                                    $containerHasChildren = \App\Models\Instruction::where('hauptmenuspalte', $instruction->hauptmenuspalte)
                                                        ->where('hauptmenu', 3)
                                                        ->exists();
                                                @endphp
                                                @if(!$containerHasChildren)
                                                    <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('Instruction/keinMenu/'.$instruction->id) }}" title="Container auflösen">
                                                        <box-icon name='chevron-left'></box-icon>
                                                    </a>
                                                    <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('Instruction/MenuMinus/'.$instruction->id) }}" title="Container zu Hauptmenüpunkt (nur ohne Unterpunkte)">
                                                        <box-icon name='chevrons-left'></box-icon>
                                                    </a>
                                                @endif
                                            @endif
                                            @if($instruction['hauptmenu']==1 && $hasPreviousDropdownContext)
                                                <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('Instruction/MenuPlus/'.$instruction->id) }}" title="Als Unterpunkt (Dropdown) einordnen">
                                                    <box-icon name='chevron-right'></box-icon>
                                                </a>
                                            @endif
                                            @if($instruction['hauptmenu']<2)
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
                                               @if($instruction->hauptmenu==1 || $instruction->hauptmenu==2 )
                                                  @php
                                                      ++$menulevel
                                                  @endphp
                                                  Hauptmenu: {{ $menulevel }}<br>
                                              @endif
                                                  @if($isChild)
                                                      <span class="text-sm font-normal text-gray-700">↳</span>
                                                  @endif
                                                  {{ $instruction->ueberschrift }}</p>
                                          <p class="mx-3 py-1 text-xs text-gray-500 font-semibold">{{ $instruction->updated_at->diffForHumans() }}</p>
                                        </div>

                                        @if(config('app.debug'))
                                            @php
                                                $debugBg = 'bg-gray-100 text-gray-800';
                                                if ($instruction->hauptmenu == 2) {
                                                    $debugBg = 'bg-blue-200 text-blue-900';
                                                } elseif ($instruction->hauptmenu == 3) {
                                                    $debugBg = 'bg-indigo-100 text-indigo-900';
                                                } elseif ($instruction->hauptmenu == 1) {
                                                    $debugBg = 'bg-emerald-100 text-emerald-900';
                                                } elseif ($instruction->hauptmenu == 0) {
                                                    $debugBg = 'bg-slate-100 text-slate-900';
                                                }
                                            @endphp
                                            <div class="mt-2 inline-block px-2 py-1 rounded text-xs {{ $debugBg }}">
                                                <span class="font-semibold">debug #{{$instruction->id}}</span>
                                                <span class="opacity-80">(HM={{ $instruction->hauptmenu }})</span>:
                                                <span class="font-mono">hauptmenuspalte={{ $instruction->hauptmenuspalte }}</span>,
                                                <span class="font-mono">position={{ $instruction->position }}</span>,
                                                <span class="font-mono">
                                                    systemmenu={{ $instruction->systemmenu }}
                                                    @if($instruction->systemmenu)
                                                        <span class="ml-1 font-semibold">SYSTEM</span>
                                                    @endif
                                                </span>
                                            </div>
                                        @endif
                                      </div>
                                  </div>

                                   @php
                                       // Kontext-Merker: Ab dem ersten Container/Unterpunkt ist "Pfeil nach rechts" erlaubt,
                                       // weil dann ein Dropdown-Kontext existiert.
                                       if ($instruction->hauptmenu == 2 || $instruction->hauptmenu == 3) {
                                           $hasPreviousDropdownContext = true;
                                       }
                                   @endphp
                              @endforeach

                              @if(isset($infoPagesWithoutMenu) && $infoPagesWithoutMenu->count() > 0)
                                  <div class="mt-8 mb-2 text-lg font-semibold text-gray-700">
                                      Informationseiten ohne Menü im Frontend
                                  </div>

                                  @foreach($infoPagesWithoutMenu as $instruction)
                                      <div class="rounded border shadow p-3 my-2 bg-blue-200">
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

                                                  {{-- Zu Hauptmenu machen (hauptmenu=1, neue Spalte, position=10) --}}
                                                  <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('Instruction/aktivMenu/'.$instruction->id) }}" title="Zu Hauptmenu machen">
                                                      <box-icon name='chevrons-right'></box-icon>
                                                  </a>
                                              </div>

                                              <div class="flex">
                                                  <p class="font-bold text-lg">
                                                      @if($instruction->systemmenu==1)
                                                          Systemprogramm<br>
                                                      @endif
                                                      {{ $instruction->ueberschrift }}
                                                  </p>
                                                  <p class="mx-3 py-1 text-xs text-gray-500 font-semibold">{{ $instruction->updated_at->diffForHumans() }}</p>
                                              </div>

                                              @if(config('app.debug'))
                                                  <div class="mt-2 inline-block px-2 py-1 rounded text-xs bg-blue-100 text-blue-900">
                                                      <span class="font-semibold">debug</span>
                                                      <span class="opacity-80">(HM={{ $instruction->hauptmenu }})</span>:
                                                      <span class="font-mono">hauptmenuspalte={{ $instruction->hauptmenuspalte }}</span>,
                                                      <span class="font-mono">position={{ $instruction->position }}</span>,
                                                      <span class="font-mono">systemmenu={{ $instruction->systemmenu }}</span>
                                                  </div>
                                              @endif
                                          </div>
                                      </div>
                                  @endforeach
                              @endif

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
