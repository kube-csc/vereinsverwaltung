<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic;

use App\Models\SportSection;

class Sectionspport extends Component
{

  use WithPagination;

  //public $abteilungs;  // Wegen Verwendung abteilungs in public function render() verwendet wird
  public $newAbteilung;
  public $newDomain;
  public $image;

  //protected $fillable = [];

  protected $listeners = ['fileUpload' => 'handleFileUpload'];

  public function handleFileUpload($imageData)
  {
          $this->image = $imageData;
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
    $this->validate(['newDomain'    => 'max:255']);

    $image             = $this->storeImage();
    $createdAbteilung  = SportSection::create([
        'abteilung'    => $this->newAbteilung,
        'domain'       => $this->newDomain,
        'idtermin'     => '0',
        'idabteilung'  => '0',
        'status'       => '2',
        'iduser'       => auth()->user()->id,
        'bild'         => $image,
      ]);
    $this->newAbteilung='';
    $this->newDomain   = '';
    $this->image       = '';
    session()->flash('message', 'Neue Abteilung '.$this->newAbteilung.' angelegt.');
  }

  public function storeImage()
  {
      if (!$this->image) {
          return null;
      }

      $img   = ImageManagerStatic::make($this->image)->encode('jpg');
      $name  = Str::random() . '.jpg';
      Storage::disk('public')->put($name, $img);
      return $name;
  }

  public function remove($abteilungid)
  {
    $abteilung = SportSection::find($abteilungid);
    Storage::disk('public')->delete($abteilung->bild);
    $abteilung->delete();
    session()->flash('message', 'Abteilung '.$abteilung->abteilung.' gelöscht.');
  }

  public function render()
  {
     return view('livewire.sectionspport', [
        'abteilungs' => SportSection::where('idabteilung' , '=' , '0')->latest()->paginate(5),
      ]);
  }
}
