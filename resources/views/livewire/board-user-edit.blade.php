<div class="bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-2">
    <div class="p-6">
        <div class="flex items-center">
            <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">
                Posten {{ $board->postenMaenlich }} / {{ $board->postenWeiblich }}  bearbeiten
            </div>
        </div>

        <div class="ml-12">
            <div class="mt-2 text-sm text-gray-500">
                <form  wire:submit.prevent="updateNummer">
                    <div class="my-4" >
                        @if (session()->has('message'))
                            <div class="p-3 bg-green-300 text-green-800 rounded shadow-sm">
                                {{ session('message') }}
                            </div>
                        @endif
                            <label for="number">Nummer (0 bis 99)</label>
                            <input id='number' type="number" min="0" max="99"
                               class="w-full rounded border shadow p-2 mr-2 my-2"
                               placeholder="1"
                               wire:model="newNummer">
                            @error('newNummer')
                              <div class="text-red-600 text-sm">{{ $message }}</div>
                            @enderror
                        <label for="positionEmail">E-Mail des Posten</label>
                        <input id='positionEmail' type="email" size="2"
                               class="w-full rounded border shadow p-2 mr-2 my-2"
                               placeholder="E-Mail des Posten"
                               wire:model="newPostenemail">
                            @error('newPostenemail')
                              <div class="text-red-600 text-sm">{{ $message }}</div>
                            @enderror
                    </div>
                    <div class="py-2">
                        <button type="submit" class="p-2 bg-blue-500 w-40 rounded shadow text-white">Änderungen speichern</button>
                    </div>
                </form>
                <br>
                <a class="p-2 bg-blue-500 w-40 rounded shadow text-white" href="/Posten/{{ $boardUser->board_id }}">
                    <i class="fas fa-arrow-circle-up"></i>
                    Zurück</a>

            </div>
        </div>

    </div>
</div>
