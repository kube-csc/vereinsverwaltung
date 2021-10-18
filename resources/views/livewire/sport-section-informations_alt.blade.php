<div style="text-align: left">
    <div>
        @if (session()->has('eventmessage'))
        <div class="p-3 bg-green-300 text-green-800 rounded shadow-sm">
            {{ session('eventmessage') }}
        </div>
        @endif
    </div>

    @foreach($events as $event)

    <div class="rounded border shadow p-3 my-2">
      <h2>Titelbild</h2>
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
      <label for="name">Domain:</label><br>
    <?php // TODO: Value wird nicht im Eingabefeld angezeigt weinn Placeholderangeben ist ?>
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
      <label for="name">Farbe der Webseite:</label><br>
       <?php // TODO: Value wird nicht im Eingabefeld angezeigt weinn Placeholderangeben ist ?>
      <form class="my-4 flex" wire:submit.prevent="updateColor">
        <div>
         <input type="text" class="w-full rounded border shadow p-2 mr-2 my-2" placeholder="Farbe der Webseite" wire:model.debounce.500ms="newColor">
        </div>
        <div class="py-2">
         <button type="submit" class="p-2 bg-blue-500 w-40 rounded shadow text-white" wire:click="updateColor({{ $this->sectionsportId }})">Updaten Farbe</button>
        </div>
      </form>
    </div>

    <div class="rounded border shadow p-3 my-2">
      <h1>Beschreibung:</h1>
        <div>
         <p>
          {!! $this->newBeschreibung !!}
         </p>
        </div>
     </div>

     <div class="rounded border shadow p-3 my-2">
       <label for="name">Beschreibung:</label><br>
       <form class="my-4 flex" wire:submit.prevent="updateDescription">
         <div>
          <textarea rows="25" cols="250" name="newDescription" class="w-full rounded border shadow p-2 mr-2 my-2" wire:model.debounce.500ms="newDescription">
            {{ $this->newBeschreibung }}
          </textarea>
         </div>
         <div class="py-2">
          <button type="submit" class="p-2 bg-blue-500 w-40 rounded shadow text-white" wire:click="updateDescription({{ $event->id }})">Beschreibung speichern</button>
         </div>
      </form>
     </div>

    @endforeach

    @if (session('eventCount') == 0 & $this->sectionsportId > 0 )

      <div class="rounded border shadow p-3 my-2">
        <h2>Titelbild</h2>
        @if( $this->sportSectionPicture )
         <box-icon name='x' wire:click="removeHeader({{ $this->sectionsportId }})"></box-icon>
         <img src="/storage/{{ $this->sportSectionPicture }}" />
        @else

          @if($this->image)
           <img src={{$this->image}} width="100" />
          @endif
          <input type="file" id="image" wire:change="$emit('fileChoosen')">
          <button type="submit" class="p-2 bg-blue-500 w-40 rounded shadow text-white" wire:click="addHeader({{ $this->sectionsportId }})">Updaten Titelbild</button>

         @endif
        </div>

        <div class="rounded border shadow p-3 my-2">
          <label for="name">Domain:</label><br>
        <?php // TODO: Value wird nicht im Eingabefeld angezeigt weinn Placeholderangeben ist ?>
          <form class="my-4 flex" wire:submit.prevent="updateDomian">
            <div>
              <input type="text" class="w-full rounded border shadow p-2 mr-2 my-2" placeholder="Domain" wire:model.debounce.500ms="newDomain">
            </div>
            <div class="py-2">
              <button type="submit" class="p-2 bg-blue-500 w-40 rounded shadow text-white" wire:click="updateDomian({{ $this->sectionsportId }})">Domain speichern</button>
            </div>
          </form>
        </div>

        <div class="rounded border shadow p-3 my-2">
          <label for="name">Farbe der Webseite:</label><br>
           <?php // TODO: Value wird nicht im Eingabefeld angezeigt weinn Placeholderangeben ist ?>
          <form class="my-4 flex" wire:submit.prevent="updateColor">
            <div>
             <input type="text" class="w-full rounded border shadow p-2 mr-2 my-2" placeholder="Farbe der Webseite" wire:model.debounce.500ms="newColor">
            </div>
            <div class="py-2">
             <button type="submit" class="p-2 bg-blue-500 w-40 rounded shadow text-white" wire:click="updateColor({{ $this->sectionsportId }})">Farbe speichen</button>
            </div>
          </form>
        </div>

        <div class="rounded border shadow p-3 my-2">
          <label for="name">Beschreibung:</label><br>
          <form class="my-4 flex" wire:submit.prevent="addDescription">
            <div>
             <textarea rows="25" cols="250" class="w-full rounded border shadow p-2 mr-2 my-2" wire:model.debounce.500ms="newDescription">
               Beschreibe hier die Abteilung ...
             </textarea>  <?php // TODO: Vorgabetext wir nicht angezeigt ?>
            </div>
            <div class="py-2">
             <button type="submit" class="p-2 bg-blue-500 w-40 rounded shadow text-white" wire:click="addDescription({{ $this->sectionsportId }})">Beschreibung speichern</button>
            </div>
          </form>
       </div>
      @endif

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
