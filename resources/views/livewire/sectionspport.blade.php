<div style="text-align: left">

  <form class="my-4 flex" wire:submit.prevent="newAbteilung">
    <div>
      <input type="text" class="w-full rounded border shadow p-2 mr-2 my-2" placeholder="Sportabteilung"
          wire:model.debounce.500ms="newAbteilung">
        </div>
        <div>
      <input type="text" class="w-full rounded border shadow p-2 mr-2 my-2" placeholder="Domain"
          wire:model.debounce.500ms="newDomain">
      </div>
      <div class="py-2">
          <button type="submit" class="p-2 bg-blue-500 w-40 rounded shadow text-white" wire:click="newAbteilung">neue Abteilung</button>
      </div>
  </form>

  @foreach ( $abteilungs as $abteilung)
  <div class="rounded border shadow p-3 my-2">
      <div class="flex justify-between my-2">
          <div class="flex">
              <p class="font-bold text-lg">{{ $abteilung ['abteilung'] }}</p>
              <p class="mx-3 py-1 text-xs text-gray-500 font-semibold">{{$abteilung->updated_at}}</p>  <!-- TODO: Ergänzen wenn datum nicht überall Null ist ->diffForHumans()  -->
          </div>
          @if($abteilung['status']==1)
           <i class="fas fa-times text-red-200 hover:text-red-600 cursor-pointer">haupt</i>
          @endif
          @if($abteilung['status']==2)
           <i class="fas fa-times text-red-200 hover:text-red-600 cursor-pointer">aktiv</i>
          @endif
          @if($abteilung['status']==0)
           <i class="fas fa-times text-red-200 hover:text-red-600 cursor-pointer">inaktiv</i>
          @endif
      </div>
      <p class="text-gray-800">{{ $abteilung ['domain'] }}</p>
      @if($abteilung['bild'] )
       Bild
      @endif
  </div>
  @endforeach


  <button wire:click="increment">+</button>
  <h1>{{ $count }}</h1>
  <button wire:click="decrement">-</button>
</div>
