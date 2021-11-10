@error('newAbteilung')
  <span class="text-red-500 text-xs">{{ $message }}</span>
@enderror

<div style="text-align: left">
  <div>
      @if (session()->has('message'))
      <div class="p-3 bg-green-300 text-green-800 rounded shadow-sm">
          {{ session('message') }}
      </div>
      @endif
  </div>

  <form class="my-4 flex" wire:submit.prevent="addAbteilung">
    <div>
      <input type="text" class="w-full rounded border shadow p-2 mr-2 my-2" placeholder="Sportabteilung" wire:model.debounce.500ms="newAbteilung">
    </div>
    <div class="py-2">
      <button type="submit" class="p-2 bg-blue-500 w-40 rounded shadow text-white" wire:click="addAbteilung">neue Abteilung</button>
    </div>
  </form>

  @foreach ( $abteilungs as $abteilung)
  <div class="rounded border shadow p-3 my-2 {{$active == $abteilung->id ? 'bg-green-200':''}}" wire:click="$emit('sportSectionSelected',{{ $abteilung->id }})">
      <div class="flex justify-between my-2">
        <div class="flex">
          <p class="font-bold text-lg">{{ $abteilung ['abteilung'] }} </p>
          <p class="mx-3 py-1 text-xs text-gray-500 font-semibold">{{ $abteilung->updated_at->diffForHumans() }}</p>
        </div>
        <?php // TODO: fas da-times bearebiten ?>
        @if($abteilung['status']==1)
          <i class="fas fa-times text-red-200 hover:text-red-600 cursor-pointer">Startseite</i>
        @endif
        @if($abteilung['status']==2)
          <box-icon name='power-off'></box-icon>
          <!-- <i class="fas fa-times text-red-200 hover:text-red-600 cursor-pointer">aktiv</i> -->
        @endif
        @if($abteilung['status']==0)
          <box-icon name='hide'></box-icon>
          <!-- <i class="fas fa-times text-red-200 hover:text-red-600 cursor-pointer">inaktiv</i> -->
        @endif
        @if ($abteilung->idtermin==0)
          <box-icon name='x' wire:click="remove({{ $abteilung->id }})"></box-icon>
        @endif
      </div>
  </div>
  @endforeach

  {{ $abteilungs->links('pagination-links') }}

</div>
