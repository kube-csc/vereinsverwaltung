<?php

namespace App\Http\Livewire;

use App\Models\Report;
use Livewire\Component;

class EventGallery extends Component
{
    public $reportId;

    public function render()
    {
        $reports  = Report::where('event_id' , $this->reportId)
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
