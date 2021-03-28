<?php

namespace App\Http\Livewire;

use Livewire\Component;

use App\Models\SportSection;
use Livewire\WithPagination;

class Sectionspport extends Component
{

  use WithPagination;

  //public $abteilungs;  // Wegen Verwendung abteilungs in public function render() verwendet wird
  public $newAbteilung;
  public $newDomain;

  protected $fillable = [];

/*
  public function mount()
  {
    $inicialsectionspport = SportSection::latest()->get();  // TODO: Alphabetisch soltierento
    // $inicialsectionspport ->takeUntil(function ($status) {return $status >= 1;});
    $this->abteilungs = $inicialsectionspport->where('status' , '>' , '0')->where('idabteilung' , '=' , '0');
    // ->orderBy('created_at','DESC')
    //$this->abteilungs = $this->abteilungs->where('status' , '>' , '0');
    // ->orderBy('created_at','DESC')[[
  }
*/

  public function addAbteilung()
  {
   /*
    if ($this->newAbteilung == '') {
     return;
    }
    */
   $this->validate(['newAbteilung' => 'required|max:20']);
   $this->validate(['newDomain'    => 'max:255']);

   $createdAbteilung = SportSection::create([
      'abteilung'    =>  $this->newAbteilung,
      'domain'       =>  $this->newDomain,
      'status'       => '2',
      'iduser'   => auth()->user()->id
    ]);
   //  $this->abteilungs->push($createdAbteilung);
   // $this->abteilungs->push($createdAbteilung);

   $this->newAbteilung="";
   $this->newDomain="";
   session()->flash('message', 'Neue Abteilung '.$this->newAbteilung.' angelegt.');
  }

  public function remove($abteilungid)
  {
    $Component = SportSection::find($abteilungid);
  //Storage::disk('public')->delete($comment->image);
    $Component->delete();
   //$this->abteilungs = $this->abteilungs->except($abteilungid);
    session()->flash('message', 'Abteilung '.$Component->abteilung.' gelöscht.');
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
      return view('livewire.sectionspport', [
        'abteilungs' => SportSection::where('status' , '>' , '0')->where('idabteilung' , '=' , '0')->latest()->paginate(5),
      ]);  // TODO: paginate Views in dr blade funktiniert nicht
  }
}
