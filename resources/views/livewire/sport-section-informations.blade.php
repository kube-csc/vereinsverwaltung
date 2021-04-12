<div style="text-align: left">
    <div>
        @if (session()->has('evevtmessage'))
        <div class="p-3 bg-green-300 text-green-800 rounded shadow-sm">
            {{ session('evevtmessage') }}
        </div>
        @endif
    </div>
    @foreach($events as $event)

    <div class="rounded border shadow p-3 my-2">
      <h2>Headerbild</h2>
      @if( $this->sportSectionPicture )

         <box-icon name='x' wire:click="removeHeader({{ $this->sectionsportId }})"></box-icon>

       <img src="/storage/{{ $this->sportSectionPicture }}" />
      @else

        @if($this->image)
         <img src={{$this->image}} width="100" />
        @endif
        <input type="file" id="image" wire:change="$emit('fileChoosen')">
        <button type="submit" class="p-2 bg-blue-500 w-40 rounded shadow text-white" wire:click="addHeader({{ $this->sectionsportId }})">Updaten Headerbild</button>

      @endif
    </div>

    <div class="rounded border shadow p-3 my-2">
      <form class="my-4 flex" wire:submit.prevent="updateDomian">
        <div>
          <input type="text" class="w-full rounded border shadow p-2 mr-2 my-2" placeholder="Domain" wire:model.debounce.500ms="newDomain">
        </div>
        <div class="py-2">
          <button type="submit" class="p-2 bg-blue-500 w-40 rounded shadow text-white" wire:click="updateDomian({{ $this->sectionsportId }})">Updaten Domain</button>
        </div>
      </form>
    </div>

    <div class="rounded border shadow p-3 my-2">
         <p class="text-gray-800">{{$event->beschreibung}}</p>
    </div>

    @endforeach
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
