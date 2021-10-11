<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Event;
use Illuminate\Support\Carbon;
use Livewire\WithPagination;


class EventFutureAll extends Component
{
    use WithPagination;

    public function render()
    {
        $eventsFuture = event::where([
            ['verwendung' , '0'],
            ['datumbis' ,'>=', Carbon::now()->toDateString()]
        ])
            ->orderby('datumbis' , 'desc')
            ->paginate(4);

        return view('livewire.event-future-all' , [
            'eventsFuture' => $eventsFuture ,
        ]);
    }
}
