<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;

use App\Models\SportSection;

class Sectionspport extends Component
{

  use WithPagination;

  //public $abteilungs;  // Wegen Verwendung abteilungs in public function render() verwendet wird
  public $newAbteilung;
  public $eventId ;
  public $active ;

  //protected $fillable = [];

  protected $listeners = [
    'sportSectionSelected',
  ];

  public function sportSectionSelected($sectionsportId)
  {
      $this->active = $sectionsportId;
  }

  public function updated($field)
  {
    $this->validateOnly($field, ['newAbteilung' => 'required|max:40']);
  }

  public function addAbteilung()
  {
    if ($this->newAbteilung == '') {
     return;
    }

    $this->validate(['newAbteilung' => 'required|max:40']);

    $createdAbteilung  = SportSection::create([
        'abteilung'        => $this->newAbteilung,
        'idtermin'         => '0',
        'sportSections_id' => '0',
        'status'           => '2',
        'user_id'          => auth()->user()->id,
      ]);
    $this->newAbteilung= '';

    session()->flash('message', 'Neue Abteilung '.$this->newAbteilung.' wurde angelegt.');
  }

  public function remove($abteilungid)
  {
    $abteilung = SportSection::find($abteilungid);
    Storage::disk('public')->delete($abteilung->bild);
    $abteilung->delete();
    session()->flash('message', 'Die Abteilung '.$abteilung->abteilung.' wurde gelöscht.');
  }

  public function render()
  {
     return view('livewire.sectionspport', [
        'abteilungs' => SportSection::where('idabteilung' , '=' , '0')->latest()->paginate(8),
      ]);
  }
}
