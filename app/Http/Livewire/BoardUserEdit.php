<?php

namespace App\Http\Livewire;

use App\Models\board;
use App\Models\boardUser;
use Livewire\Component;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class BoardUserEdit extends Component
{
    public $boardUserId;
    public $postenbild;
    public $newNummer;
    public $newPostenemail;

    // ToDo: Validate funktioniert nicht die Valedierung wird in @board-use-edit.blade.php überprüft
      protected $rules = [
          'newNummer'       => 'required|integer|min:0|max:99',
          'newPostenemail' => 'required|email',
    ];

    protected $messages = [
        'newNummer.required'      => 'Nummer ist erforderlich.',
        'newNummer.integer'       => 'Nummer muss eine Zahl sein.',
        'newNummer.min'           => 'Nummer muss mindestens 0 sein.',
        'newNummer.max'           => 'Nummer darf höchstens 99 sein.',
        'newPostenemail.required' => 'E-Mail ist erforderlich.',
        'newPostenemail.email'    => 'E-Mail-Format ist ungültig.',
    ];

    public function updated($field)
    {
        $this->validateOnly($field);
    }

    public function updateNummer()
    {
      $this->validate();

        $nummer = (int) ($this->newNummer ?? 0);

        boardUser::findOrFail($this->boardUserId)->update([
            'nummer'         => $nummer,
            'postenemail'    => trim($this->newPostenemail),
            'bearbeiter_id'  => Auth::id(),
            'updated_at'     => Carbon::now(),
        ]);

       // $this->newNummer = '';
        session()->flash('message', 'Daten wurden gespeichert.');

        $boardUser = boardUser::findOrFail($this->boardUserId);
        return redirect()->route('boardUser.index', [ 'board_id' => $boardUser->board_id, ]);
    }

    public function mount()
    {
       $boardUser = boardUser::findOrFail($this->boardUserId);
       $this->newNummer      = $boardUser->nummer;
       $this->newPostenemail = $boardUser->postenemail;
     }

    public function render()
    {
        $boardUser = boardUser::with('board')->findOrFail($this->boardUserId);
        //$board         = board::find($boardUser->board_id); Temp:: Anpassung Livewire 3
        return view('livewire.board-user-edit', [
            // 'boardUserId'      => $this->boardUserId, Temp:: Anpassung Livewire 3
            //'newNummer'     => $boardUser->nummer,  Temp:: Anpassung Livewire 3
            'boardUser' => $boardUser,
            'board' => $boardUser->board,
            ]);
    }
}
