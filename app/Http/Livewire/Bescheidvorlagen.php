<?php

namespace App\Http\Livewire;

use App\Models\NoticeTemplate;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Bescheidvorlagen extends Component
{
    public array $titel = [];
    public array $text = [];

    public function mount(): void
    {
        abort_unless(Gate::allows('antraege.foerdermittel_verwalten'), 403);

        foreach (array_keys(NoticeTemplate::labels()) as $type) {
            $template = NoticeTemplate::forType($type);
            $this->titel[$type] = $template->titel;
            $this->text[$type] = $template->text;
        }
    }

    public function save(string $type): void
    {
        abort_unless(Gate::allows('antraege.foerdermittel_verwalten'), 403);
        abort_unless(array_key_exists($type, NoticeTemplate::labels()), 404);

        $this->validate([
            'titel.' . $type => ['required', 'string', 'max:255'],
            'text.' . $type => ['required', 'string', 'max:5000'],
        ], [
            'titel.' . $type . '.required' => 'Bitte einen Titel angeben.',
            'text.' . $type . '.required' => 'Bitte einen Text angeben.',
        ]);

        NoticeTemplate::updateOrCreate(['type' => $type], [
            'titel' => $this->titel[$type],
            'text' => $this->text[$type],
        ]);

        session()->flash('message', 'Vorlage „' . NoticeTemplate::labels()[$type] . '“ gespeichert.');
    }

    public function resetToDefault(string $type): void
    {
        abort_unless(Gate::allows('antraege.foerdermittel_verwalten'), 403);
        abort_unless(array_key_exists($type, NoticeTemplate::labels()), 404);

        NoticeTemplate::where('type', $type)->delete();
        $default = NoticeTemplate::defaults()[$type];
        $this->titel[$type] = $default['titel'];
        $this->text[$type] = $default['text'];
        session()->flash('message', 'Standardtext wiederhergestellt.');
    }

    public function render()
    {
        return view('livewire.bescheidvorlagen', [
            'labels' => NoticeTemplate::labels(),
            'placeholders' => NoticeTemplate::PLACEHOLDERS,
        ]);
    }
}
