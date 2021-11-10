<div class="bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-2">
    <div class="p-6">
        <div class="flex items-center">
            <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">
                Posten {{ $boardUser->nummer > 0 ? $boardUser->nummer.')' : '' }} {{ $board->postenmaenlich }} / {{ $board->postenweiblich }}  bearbeiten
            </div>
        </div>

        <div class="ml-12">
            <div class="mt-2 text-sm text-gray-500">

                <form wire:submit.prevent="updateBoardUser">
                    @csrf
                    @php
                        // TODO:  @method('PUT') im Hobby Projekt noch mal erlernen
                    @endphp

                    <div class="my-4" >
                        @if (session()->has('message'))
                            <div class="p-3 bg-green-300 text-green-800 rounded shadow-sm">
                                {{ session('message') }}
                            </div>
                        @endif
                      <div class="w-full rounded border shadow p-2 mr-2 my-2">

                          @if(isset($boardUser->boardUserName->vorname))
                            <label>Posten:</label>
                            <p>
                                {{ $boardUser->boardUserName->vorname }} {{ $boardUser->boardUserName->nachname }}
                            </p>
                            <br>
                          @endif

                            <label for="number">Filter Mitglieder:</label>
                            <input id='searchUser' type="text" class="w-full rounded border shadow p-2 mr-2 my-2" wire:model.debounce.500ms="searchUser">
                            <box-icon name='minus' wire:click="$emit('userSelected', 0 )"></box-icon>
                        @foreach ($users as $user)
                           <div class="rounded border shadow p-3 my-2 {{ $userSelected == $user->id ? 'bg-blue-300' : 'bg-blue-200' }}" wire:click="$emit('userSelected',{{ $user->id }})">
                            {{ $user->vorname }} {{ $user->nachname }}
                           </div>
                        @endforeach
                      </div>
                            <div class="w-full rounded border shadow p-2 mr-2 my-2">
                                <label for="postenbild">Neues Porträt:</label>
                                @if($image)
                                    <img src={{ $image }} width="200" />
                                @else
                                    @if($savedImage)
                                      <img src={{ $savedImage }} width="200" />
                                    @else
                                      <p>kein Porträt zum hochladen vorhanden</p>
                                    @endif
                                @endif
                                <input type="file" id="image" wire:change="$emit('fileChoosen')">
                            </div>
                        @if($currentImage!='')
                               <div class="w-full rounded border shadow p-2 mr-2 my-2">
                                    <label for="postenbild">Porträt:</label>
                                    <div class="flex ml-2">
                                        <div class="flex-initial"><img src={{ '/storage/posten/'.$currentImage }} width="200" /></div>
                                        <div class="flex-initial ml-2 fas fa-times text-red-600 hover:text-red-00 cursor-pointer" wire:click="$emit('deletionNote')">x</div>
                                    </div>
                               </div>
                         @endif
                    </div>
                    <div class="py-2">
                        <button type="submit" class="p-2 bg-blue-500 w-40 rounded shadow text-white">Änderungen speichern</button>
                    </div>
                </form>

                {{ $users->links() }}

                <br>
                <a class="p-2 bg-blue-500 w-40 rounded shadow text-white" href="/Posten/{{ $boardUser->board_id }}"><i class="fas fa-arrow-circle-up"></i>Zurück</a>

            </div>
        </div>

    </div>
</div>

<script>
    window.livewire.on('fileChoosen', () => {
        let inputField = document.getElementById('image')
        let file = inputField.files[0]
        let reader = new FileReader();
        reader.onloadend = () => {
            window.livewire.emit('fileUpload', reader.result)
        }
        reader.readAsDataURL(file);

    })
</script>
