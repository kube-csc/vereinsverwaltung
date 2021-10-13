<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Event;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Livewire\WithPagination;

class EventFutureAll extends Component
{
    use WithPagination;

    public $month;
    public $year;
    public $search;

    public function mount()
    {
       $year= Carbon::now()->format('Y');
       $this->year = $year;
    }

    public function render()
    {
        if(Str::length($this->year)>4)
        {
            $this->year=Carbon::now()->format('Y');
        }

        if($this->month > 12) {
            $this->month = 12;
        }

        if($this->month == 0) {
            $this->month = "";
        }

        if($this->month < 1 and $this->month != "") {
            $this->month = 1;
        }

        if($this->month > 0) {
            if($this->month > 9) {
                $month = "-" . $this->month . "-";
            }
            else{
                if(substr($this->month, 0, 1)=="0") {
                    $month = "-" . $this->month . "-";
                }
                else{
                    $month = "-0" . $this->month . "-";
                }
            }

            $eventsFuture = event::where([
                ['ueberschrift' , 'LIKE' , "%{$this->search}%"],
                ['verwendung' , '0'],
                ['datumvon' , 'LIKE' , "%{$month}%"],
                ['datumvon' , 'LIKE' , "%{$this->year}%"],
                ['datumbis' ,'>=', Carbon::now()->toDateString()]
            ])
                ->orderby('datumbis' , 'desc')
                ->paginate(4);
        }
        else{

            $this->month = "";
            $eventsFuture = event::where([
                ['ueberschrift' , 'LIKE' , "%{$this->search}%"],
                ['verwendung' , '0'],
                ['datumvon' , 'LIKE' , "%{$this->year}%"],
                ['datumbis' ,'>=', Carbon::now()->toDateString()]
            ])
                ->orderby('datumbis' , 'desc')
                ->paginate(4);
        }

        return view('livewire.event-future-all' , [
            'eventsFuture' => $eventsFuture ,
        ]);
    }
}
