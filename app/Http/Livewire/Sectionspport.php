<?php

namespace App\Http\Livewire;

use Livewire\Component;

use App\Models\SportSection;

class Sectionspport extends Component
{
  public $newAbteilung;
  public $newDomain;
  public $abteilungs;

  protected $fillable = [];

  public function mount()
  {
     $inicialsectionspport = SportSection::latest()->get();  // TODO: Alphabetisch soltierento  // TODO: Filter Status >0
    // $inicialsectionspport ->takeUntil(function ($status) {return $status >= 1;});
     $this->abteilungs = $inicialsectionspport;

     // ->orderBy('created_at','DESC')
  }

  public function newAbteilung()
  {

    /*
    if ($this->newAbteilung == '') {
     return;
    }
    */

    $this->validate(['newAbteilung' => 'required|max:255']);
    $this->validate(['newDomain'    => 'max:255']);

    $createdAbteilung = SportSection::create([
      'abteilung'    =>  $this->newAbteilung,
      'domain'       =>  $this->newDomain,
      'status'       => '2',
      'idmitglied'   => auth()->user()->id
    ]);
   //  $this->abteilungs->push($createdAbteilung);
   $this->abteilungs->prepend($createdAbteilung);

   $this->newAbteilung="";
   $this->newDomain="";
  }

  public $count = 1;

  public function increment()
  {
      $this->count++;
  }

  public function decrement()
  {
      $this->count--;
  }

    public function render()
    {
        return view('livewire.sectionspport');
    }
}
