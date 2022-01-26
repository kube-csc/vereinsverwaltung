<x-app-layout>
    <x-slot name="header">
       <h2 class="font-semibold text-xl text-gray-800 leading-tight">
           {{ __('Menu') }}
       </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

              <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                  <div class="mt-8 text-2xl">
                    Menu
                  </div>

                  <div class="mt-6 text-gray-500">
                   In diesem Bereich werden ...
                  </div>

              </div>

              <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-2">
                  <div class="p-6">
                      <div class="flex items-center">
                            <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">Events / Termine</div>
                      </div>

                      <div class="ml-12">
                          <div class="mt-2 text-sm text-gray-500">

                            <div style="text-align: left">

                              <div class="rounded border shadow p-3 my-2 bg-blue-200" onclick="window.location.replace('Event/alle')">
                                  <div class="flex justify-between my-2">
                                    <div class="flex">
                                      <p class="font-bold text-lg">zukünftige Events bearbeiten</p>
                                      <p class="mx-3 py-1 text-xs text-gray-500 font-semibold"></p>
                                    </div>
                                  </div>
                              </div>
                              <div class="rounded border shadow p-3 my-2 bg-blue-200" onclick="window.location.replace('Eventvergangenheit/alle')">
                                  <div class="flex justify-between my-2">
                                    <div class="flex">
                                      <p class="font-bold text-lg">vergangende Events bearbeiten</p>
                                      <p class="mx-3 py-1 text-xs text-gray-500 font-semibold"></p>
                                    </div>
                                  </div>
                              </div>

                            </div>

                          </div>
                      </div>
                  </div>

                  <div class="p-6 border-t border-gray-200 md:border-t-0 md:border-l">
                      <div class="flex items-center">
                          <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">Abteilung / Mannschaften</div>
                      </div>

                      <div class="ml-12">
                          <div class="mt-2 text-sm text-gray-500">

                            <div style="text-align: left">

                              <div class="rounded border shadow p-3 my-2 bg-blue-200" onclick="window.location.replace('Abteilung/alle')">
                                  <div class="flex justify-between my-2">
                                    <div class="flex">
                                      <p class="font-bold text-lg">Abteilung bearbeiten</p>
                                      <p class="mx-3 py-1 text-xs text-gray-500 font-semibold"></p>
                                    </div>
                                  </div>
                              </div>

                              <div class="rounded border shadow p-3 my-2 bg-blue-200" onclick="window.location.replace('Mannschaft/alle')">
                                  <div class="flex justify-between my-2">
                                    <div class="flex">
                                      <p class="font-bold text-lg">Mannschaft bearbeiten</p>
                                      <p class="mx-3 py-1 text-xs text-gray-500 font-semibold"></p>
                                    </div>
                                  </div>
                              </div>

                            </div>

                          </div>

                      </div>
                  </div>

                  <div class="p-6 border-t border-gray-200 md:border-t-0 md:border-l">
                      <div class="flex items-center">
                          <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">Chat Bot</div>
                      </div>

                      <div class="ml-12">
                          <div class="mt-2 text-sm text-gray-500">

                            <div style="text-align: left">

                              <div class="rounded border shadow p-3 my-2 bg-blue-200" onclick="window.location.replace('newBotmanQuestion/alle')">
                                  <div class="flex justify-between my-2">
                                    <div class="flex">
                                      <p class="font-bold text-lg">neue Fragen bearbeiten</p>
                                      <p class="mx-3 py-1 text-xs text-gray-500 font-semibold"></p>
                                    </div>
                                  </div>
                              </div>

                              <div class="rounded border shadow p-3 my-2 bg-blue-200" onclick="window.location.replace('')">
                                  <div class="flex justify-between my-2">
                                    <div class="flex">
                                      <p class="font-bold text-lg">Fragen bearbeiten</p>
                                      <p class="mx-3 py-1 text-xs text-gray-500 font-semibold"></p>
                                    </div>
                                  </div>
                              </div>

                              <div class="rounded border shadow p-3 my-2 bg-blue-200" onclick="window.location.replace('')">
                                  <div class="flex justify-between my-2">
                                    <div class="flex">
                                      <p class="font-bold text-lg">Antworten bearbeiten</p>
                                      <p class="mx-3 py-1 text-xs text-gray-500 font-semibold"></p>
                                    </div>
                                  </div>
                              </div>

                            </div>

                          </div>


                      </div>
                  </div>

                  <div class="p-6 border-t border-gray-200">
                      <div class="flex items-center">
                          <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">Event Gruppen</div>
                      </div>

                      <div class="ml-12">
                          <div class="mt-2 text-sm text-gray-500">

                              <div style="text-align: left">

                                  <div class="rounded border shadow p-3 my-2 bg-blue-200" onclick="window.location.replace('Team/alle')">
                                      <div class="flex justify-between my-2">
                                          <div class="flex">
                                              <p class="font-bold text-lg">Teammanagment</p>
                                              <p class="mx-3 py-1 text-xs text-gray-500 font-semibold"></p>
                                          </div>
                                      </div>
                                  </div>

                                  <div class="rounded border shadow p-3 my-2 bg-blue-200" onclick="window.location.replace('Instruction/alle')">
                                      <div class="flex justify-between my-2">
                                          <div class="flex">
                                              <p class="font-bold text-lg">Informationsseiten</p>
                                              <p class="mx-3 py-1 text-xs text-gray-500 font-semibold"></p>
                                          </div>
                                      </div>
                                  </div>

                                  <div class="rounded border shadow p-3 my-2 bg-blue-200" onclick="window.location.replace('Dokumente/alle')">
                                      <div class="flex justify-between my-2">
                                          <div class="flex">
                                              <p class="font-bold text-lg">Dokumentenverwaltung</p>
                                              <p class="mx-3 py-1 text-xs text-gray-500 font-semibold"></p>
                                          </div>
                                      </div>
                                  </div>

                                  <div class="rounded border shadow p-3 my-2 bg-blue-200" onclick="window.location.replace('Eventgruppe/alle')">
                                      <div class="flex justify-between my-2">
                                          <div class="flex">
                                              <p class="font-bold text-lg">Event Gruppen</p>
                                              <p class="mx-3 py-1 text-xs text-gray-500 font-semibold"></p>
                                          </div>
                                      </div>
                                  </div>

                                  <div class="rounded border shadow p-3 my-2 bg-blue-200" onclick="window.location.replace('Backlink/alle')">
                                      <div class="flex justify-between my-2">
                                          <div class="flex">
                                              <p class="font-bold text-lg">Backlinks alter Weblayout</p>
                                              <p class="mx-3 py-1 text-xs text-gray-500 font-semibold"></p>
                                          </div>
                                      </div>
                                  </div>

                              </div>

                         </div>
                      </div>
                  </div>

                  <div class="p-6 border-t border-gray-200 md:border-l">
                      <div class="flex items-center">
                          <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold"></div>
                      </div>

                      <div class="ml-12">
                          <div class="mt-2 text-sm text-gray-500">

                          </div>
                      </div>
                  </div>
              </div>

            </div>
        </div>
    </div>
</x-app-layout>
