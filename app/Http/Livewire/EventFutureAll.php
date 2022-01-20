<?php

namespace App\Http\Livewire;

use App\Models\SportSection;
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
        if($this->year<1900){
            $this->year=Null;
        }
    }

    public function render()
    {
        if(Str::length($this->year)>4){
            $this->year=Carbon::now()->format('Y');
        }

        if($this->year<Carbon::now()->format('Y') && $this->year!=Null){
            $this->year=Carbon::now()->format('Y');
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

        if($this->month < 1 and $this->month != ""){
            $this->month = 1;
        }

        if($this->month > 0){
            if($this->month > 9){
                $month = "-" . $this->month . "-";
            }
            else{
                if(substr($this->month, 0, 1)=="0"){
                    $month = "-" . $this->month . "-";
                }
                else{
                    $month = "-0" . $this->month . "-";
                }
            }

            $sportSection_id = $this->sportSection_id;

            if($sportSection_id>0){
                $eventsFuture = event::where('ueberschrift', 'LIKE', '%'.$this->search.'%')
                                        ->where('verwendung' , '0')
                                        ->where('datumvon' , 'LIKE' , '%'.$month.'%')
                                        ->where('datumbis' , '>=' , Carbon::now()->toDateString())
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
                $eventsFuture = event::where('ueberschrift', 'LIKE', '%'.$this->search.'%')
                                        ->where('verwendung' , '0')
                                        ->where('datumvon' , 'LIKE' , '%'.$month.'%')
                                        ->where('datumbis' , '>=' , Carbon::now()->toDateString())
                                        ->where(function($quiet2) use($year){
                                            $quiet2->where('datumvon' , 'LIKE' , '%'.$year.'%')
                                                ->orWhere('datumbis' , 'LIKE' , '%'.$year.'%');
                                        })
                                        ->orderby('datumvon', 'desc')
                                        ->paginate(4);
              }
        }
        else{
            $this->month = "";
            $year = $this->year;
            $sportSection_id = $this->sportSection_id;

            if($sportSection_id>0){
                $eventsFuture = event::where('ueberschrift', 'LIKE', '%'.$this->search.'%')
                                        ->where('verwendung' , '0')
                                        ->where('datumbis' , '>=' , Carbon::now()->toDateString())
                                        ->where(function($quiet1) use($sportSection_id){
                                          $quiet1->where('sportSection_id' , $sportSection_id)
                                                 ->orWhere('sportSection_id' , Null);
                                        })
                                        ->where(function($quiet2) use($year){
                                          $quiet2->where('datumvon' , 'LIKE' , '%'.$year.'%')
                                                 ->orWhere('datumbis' , 'LIKE' , '%'.$year.'%');
                                        })
                                        ->orderby('datumbis' , 'desc')
                                        ->paginate(4);
            }
            else{
                $eventsFuture = event::where('ueberschrift', 'LIKE', '%'.$this->search.'%')
                                        ->where('verwendung' , '0')
                                        ->where('datumbis' , '>=' , Carbon::now()->toDateString())
                                        ->where(function($quiet2) use($year){
                                            $quiet2->where('datumvon' , 'LIKE' , '%'.$year.'%')
                                                   ->orWhere('datumbis' , 'LIKE' , '%'.$year.'%');
                                        })
                                        ->orderby('datumvon', 'desc')
                                        ->paginate(4);
            }
        }

        $eventsFutureCount=$eventsFuture->count();

        $sportSections = SportSection::where('status' , '>' ,'0')->orderby('status')
            ->orderby('sportSection_id')
            ->orderby('abteilung')
            ->get();

        return view('livewire.event-future-all' , [
            'eventsFuture'      => $eventsFuture,
            'eventsFutureCount' => $eventsFutureCount,
            'sportSections'     => $sportSections
        ]);
    }
}
