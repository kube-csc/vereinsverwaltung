<?php

namespace App\Http\Livewire;

use App\Models\report;
use Livewire\Component;

class EventGallery extends Component
{
    public $reportId;

    public function render()
    {
        $reports  = report::where('event_id' , $this->reportId)
            ->where('visible' , 1)
            ->where('typ' , 1)
            ->orderby('position')
            ->get();

        return view('livewire.event-gallery' ,
                    [
                        'reports'        => $reports,
                    ]);
    }
}
