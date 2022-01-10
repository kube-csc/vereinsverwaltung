<?php

namespace App\Http\Livewire;

use App\Models\SportSection;
use Livewire\Component;
use App\Models\Event;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Livewire\WithPagination;

class EventPastAll extends Component
{
    use WithPagination;

    public $month;
    public $year;
    public $search;
    public $sportSection_id;

    public function mount()
    {
        $this->year= Carbon::now()->format('Y');
        if($this->year<1900  && $this->year!=Null){
            $this->year=1900;
        }
        if($this->year>2050){
            $this->year=2050;
        }
        $this->sportSection_id=0;
    }

    public function monthIncrease ()
    {
        ++$this->month;
    }

    public function monthDecrease ()
    {
        --$this->month;
    }

    public function yearIncrease()
    {
        ++$this->year;
    }

    public function yearDecrease()
    {
        --$this->year;
    }

    public function render()
    {
        if(Str::length($this->year)>4)
        {
            $this->year=Carbon::now()->format('Y');
        }

        if($this->year<1900 && $this->year!=Null){
            $this->year=1900;
        }
        if($this->year>2050){
            $this->year=2050;
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

        if($this->month > 0){
            if($this->month > 9){
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
            if($this->sportSection_id>0){
                $eventsPast = event::where([
                    ['ueberschrift', 'LIKE', "%{$this->search}%"],
                    ['verwendung', '0'],
                    ['datumvon', 'LIKE', "%{$month}%"],
                    ['datumvon', 'LIKE', "%{$this->year}%"],
                    ['datumbis', '<=', Carbon::now()->toDateString()],
                    ['sportSection_id', $this->sportSection_id]
                ])
                    ->orderby('datumbis', 'desc')
                    ->paginate(4);
            }
            else{
                $eventsPast = event::where([
                    ['ueberschrift' , 'LIKE' , "%{$this->search}%"],
                    ['verwendung' , '0'],
                    ['datumvon' , 'LIKE' , "%{$month}%"],
                    ['datumvon' , 'LIKE' , "%{$this->year}%"],
                    ['datumbis' ,'<=', Carbon::now()->toDateString()]
                ])
                    ->orderby('datumbis' , 'desc')
                    ->paginate(4);
            }
        }
        else{
            $this->month = "";
            if($this->sportSection_id>0) {
                $eventsPast = event::where([
                    ['ueberschrift', 'LIKE', "%{$this->search}%"],
                    ['verwendung', '0'],
                    ['nachtermin', '!=', ""],
                    ['datumvon', 'LIKE', "%{$this->year}%"],
                    ['datumbis', '<=', Carbon::now()->toDateString()],
                    ['sportSection_id', $this->sportSection_id]
                ])
                    ->orderby('datumbis', 'desc')
                    ->paginate(4);
            }
            else{
                $eventsPast = event::where([
                    ['ueberschrift', 'LIKE', "%{$this->search}%"],
                    ['verwendung', '0'],
                    ['nachtermin', '!=', ""],
                    ['datumvon', 'LIKE', "%{$this->year}%"],
                    ['datumbis', '<=', Carbon::now()->toDateString()]
                ])
                    ->orderby('datumbis', 'desc')
                    ->paginate(4);
            }
        }

        $eventsPastCount=$eventsPast->count();

        $sportSections = SportSection::where('status' , '>' ,'0')->orderby('status')
            ->orderby('sportSection_id')
            ->orderby('abteilung')
            ->get();

        return view('livewire.event-past-all' , [
            'eventsPast' => $eventsPast,
            'eventsPastCount' => $eventsPastCount,
            'sportSections'     => $sportSections
        ]);
    }
}
