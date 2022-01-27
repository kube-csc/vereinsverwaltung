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
    public $searchSave;
    public $monthSave;
    public $yearSave;
    public $sportSection_idSave;

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
        if(Str::length($this->year)>4){
            $this->year=Carbon::now()->format('Y');
        }

        if($this->year<1900 && $this->year!=Null){
            $this->year=1900;
            $this->resetPage();
        }

        if($this->year>Carbon::now()->format('Y')){
            $this->year=Carbon::now()->format('Y');
        }

        if($this->month > 12) {
            $this->month = 12;
        }

        if($this->month == 0) {
            $this->month = "";
        }

        if($this->month < 1 and $this->month != ""){
            $this->month = 1;
        }

        if($this->searchSave<>$this->search){
            $this->searchSave=$this->search;
            $this->resetPage();
        }

        if($this->yearSave<>$this->year){
            $this->yearSave=$this->year;
            $this->resetPage();
        }

        if($this->sportSection_idSave<>$this->sportSection_id){
            $this->sportSection_idSave=$this->sportSection_id;
            $this->resetPage();
        }

        $sportSection_id = $this->sportSection_id;
        $year=$this->year;

        if($this->month > 0){
            if($this->month > 9){
                $this->month = "-" . $this->month . "-";
            }
            else{
                if(substr($this->month, 0, 1)=="0") {
                    $month = "-" . $this->month . "-";
                }
                else{
                    $month = "-0" . $this->month . "-";
                }
            }

            if($this->monthSave<>$month){
                $this->monthSave=$month;
                $this->resetPage();
            }

            if($sportSection_id>0){
                $eventsPast = event::where('ueberschrift', 'LIKE', '%'.$this->search.'%')
                                    ->where('verwendung' , '0')
                                    ->where('nachtermin' , '!=', "")
                                    ->where('datumbis' , '<=' , Carbon::now()->toDateString())
                                    ->where(function($quiet1) use($month){
                                        $quiet1->where('datumvon' , 'LIKE' , '%'.$month.'%')
                                               ->orWhere('datumbis' , 'LIKE' , '%'.$month.'%');
                                    })
                                    ->where(function($quiet2) use($year){
                                        $quiet2->where('datumvon' , 'LIKE' , '%'.$year.'%')
                                               ->orWhere('datumbis' , 'LIKE' , '%'.$year.'%');
                                    })
                                    ->where(function($quiet3) use($sportSection_id){
                                        $quiet3->where('sportSection_id' , $sportSection_id)
                                               ->orWhere('sportSection_id' , Null);
                                    })
                                    ->orderby('datumvon' , 'desc')
                                    ->paginate(4);
            }
            else{
                $eventsPast = event::where('ueberschrift', 'LIKE', '%'.$this->search.'%')
                                    ->where('verwendung' , '0')
                                    ->where('nachtermin' , '!=', "")
                                    ->where(function($quiet1) use($month){
                                        $quiet1->where('datumvon' , 'LIKE' , '%'.$month.'%')
                                               ->orWhere('datumbis' , 'LIKE' , '%'.$month.'%');
                                    })
                                    ->where('datumbis' , '<=' , Carbon::now()->toDateString())
                                    ->where(function($quiet2) use($year){
                                        $quiet2->where('datumvon' , 'LIKE' , '%'.$year.'%')
                                               ->orWhere('datumbis' , 'LIKE' , '%'.$year.'%');
                                    })
                                    ->orderby('datumvon' , 'desc')
                                    ->paginate(4);
            }
        }
        else{
            $this->month = "";

            if($this->monthSave<>$this->month){
                $this->monthSave=$this->month;
                $this->resetPage();
            }

            $sportSection_id = $this->sportSection_id;
            if($sportSection_id>0){
               $eventsPast = event::where('ueberschrift', 'LIKE', '%'.$this->search.'%')
                                    ->where('verwendung' , '0')
                                    ->where('nachtermin' , '!=', "")
                                    ->where('datumbis' , '<=' , Carbon::now()->toDateString())
                                    ->where(function($quiet1) use($sportSection_id){
                                        $quiet1->where('sportSection_id' , $sportSection_id)
                                            ->orWhere('sportSection_id' , Null);
                                    })
                                    ->where(function($quiet2) use($year){
                                        $quiet2->where('datumvon' , 'LIKE' , '%'.$year.'%')
                                               ->orWhere('datumbis' , 'LIKE' , '%'.$year.'%');
                                    })
                                    ->orderby('datumvon' , 'desc')
                                    ->paginate(4);
            }
            else{
                $eventsPast = event::where('ueberschrift', 'LIKE', '%'.$this->search.'%')
                                    ->where('verwendung' , '0')
                                    ->where('nachtermin' , '!=', "")
                                    ->where('datumbis' , '<=' , Carbon::now()->toDateString())
                                    ->where(function($quiet2) use($year){
                                        $quiet2->where('datumvon' , 'LIKE' , '%'.$year.'%')
                                               ->orWhere('datumbis' , 'LIKE' , '%'.$year.'%');
                                    })
                                    ->orderby('datumvon', 'desc')
                                    ->paginate(4);
            }
        }

        $eventsPastCount=$eventsPast->count();

        $sportSections = SportSection::where('status' , '>' ,'0')->orderby('status')
            ->orderby('sportSection_id')
            ->orderby('abteilung')
            ->get();

        return view('livewire.event-past-all' , [
            'eventsPast'        => $eventsPast,
            'eventsPastCount'   => $eventsPastCount,
            'sportSections'     => $sportSections,
        'sportSection_idSave'   => $this->sportSection_idSave
        ]);
    }
}
