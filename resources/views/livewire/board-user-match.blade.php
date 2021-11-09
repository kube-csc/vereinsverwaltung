<form  wire:submit.prevent="updateBoardUser">
    @csrf
    @php
        // TODO:  @method('PUT') in Hobby Projekt noch mal erlernen
    @endphp
    <div class="my-4" >
        @if (session()->has('message'))
            <div class="p-3 bg-green-300 text-green-800 rounded shadow-sm">
                {{ session('message') }}
            </div>
        @endif
            <label for="number">Filter Mitglieder:</label>
            <input id='searchUser' type="text" class="w-full rounded border shadow p-2 mr-2 my-2" wire:model.debounce.500ms="searchUser">
            <box-icon name='minus' wire:click="$emit('userSelected', 0 )"></box-icon>
        @foreach ($users as $user)
           <div class="rounded border shadow p-3 my-2 {{ $userSelected == $user->id ? 'bg-blue-300' : 'bg-blue-200' }}" wire:click="$emit('userSelected',{{ $user->id }})">
            {{ $user->vorname }} {{ $user->nachname }}
           </div>
        @endforeach
            <label for="postenbild">Porträt:</label>

    </div>
    <div class="py-2">
        <button type="submit" class="p-2 bg-blue-500 w-40 rounded shadow text-white">Änderungen speichern</button>
    </div>
</form>
<br>
<a class="p-2 bg-blue-500 w-40 rounded shadow text-white" href="/Posten/{{ $boardUser->board_id }}"><i class="fas fa-arrow-circle-up"></i>Zurück</a>
