<?php

namespace App\Http\Livewire;

use App\Models\Event;
use App\Models\SportSection;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManagerStatic;
use Carbon\Carbon;

class SportSectionInformations extends Component
{

    public $eventsectionsportId;
    public $sportSectionPicture;
    public $sectionsportId;
    public $newDomain;
    public $image;

    protected $listeners = [
      'fileUpload' => 'handleFileUpload',
      'sportSectionSelected',
    ];

    public function handleFileUpload($imageData)
    {
      $this->image = $imageData;
    }

    public function sportSectionSelected($sectionsportId)
    {
      $abteilung = SportSection::where('id' , '=' , $sectionsportId)->first();

      if (!empty($abteilung))
      {
        $this->eventsectionsportId = $abteilung->idtermin;
        $this->sportSectionPicture = $abteilung->bild;
        $this->sectionsportId      = $sectionsportId;
      }
    }

    public function updateDomian($sectionsportId)
    {
      if ($this->newDomain == '') {
       return;
      }
      $this->validate(['newDomain'    => 'max:255']);

      $abteilung = SportSection::find($sectionsportId);
      $abteilung->update([
        'domain' =>  $this->newDomain,
      ]);

      $this->newDomain   = '';

      session()->flash('evevtmessage', 'Domain '.$this->newDomain.' eingetragen.');
    }

    public function addHeader($sectionsportId)
    {
      $image             = $this->storeImage();

      $abteilung = SportSection::find($sectionsportId);
      $abteilung->update([
        'bild' =>  $image ,
      ]);

      session()->flash('evevtmessage', 'Neues Titelbild wurde hochgeladen.');
    }

    public function storeImage()
    {

      if (!$this->image)
        {
          return null;
        }

      $img   = ImageManagerStatic::make($this->image)->encode('jpg');
      $name  = 'header'.Carbon::now()->timestamp.Str::random(4).'.jpg';
      Storage::disk('public')->put($name, $img);
      return $name;
    }

    public function removeHeader($sectionsportId)
    {
      $abteilung = SportSection::find($sectionsportId);
      Storage::disk('public')->delete($abteilung->bild);

      $abteilung->update([
        'bild' => '',
      ]);

      $this->image = '';
      $this->sportSectionPicture = '';

      session()->flash('evevtmessage', 'Titelbild  '.$abteilung->bild.' wurde gelöscht.');
    }

    public function render()
    {
      return view('livewire.sport-section-informations', [
        'events' => Event::where('id' , '=' , $this->eventsectionsportId)->get(),
        'sportSectionPicture' => $this->sportSectionPicture ,
      ]);
    }
}
