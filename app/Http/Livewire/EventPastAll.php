<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Event;
use Illuminate\Support\Carbon;
use Livewire\WithPagination;

class EventPastAll extends Component
{
    use WithPagination;

    public function render()
    {
        $eventsPast = event::where([
            ['verwendung' , '0'],
            ['datumbis' ,'<=', Carbon::now()->toDateString()]
        ])
            ->orderby('datumbis' , 'desc')
            ->paginate(4);

        return view('livewire.event-past-all' , [
            'eventsPast' => $eventsPast ,
        ]);
    }
}
