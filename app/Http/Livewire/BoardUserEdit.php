<?php

namespace App\Http\Livewire;

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

    public function updated($field)
    {
        $this->validateOnly($field, [
            'newNummer'      => 'required|max:2',
            //'newPostenemail' => 'email',  Todo: Verhindert das speichern wenn keine E-Mail eingeben wurde
            ]);
    }

    public function updateNummer()
    {
       $this->validate([
           'newNummer'      => 'required|max:2',
           //'newPostenemail' => 'email', Todo: Verhindert das speichern wenn keine E-Mail eingeben wurde
       ]);
       boardUser::find($this->boardUserId)->update([
            'nummer'           => $this->newNummer,
            'postenemail'      => $this->newPostenemail,
            'bearbeiter_id'    => Auth::user()->id,
            'updated_at'       => Carbon::now()
        ]);

       // $this->newNummer = '';
        session()->flash('message', 'Daten wurden gespeichert.');

       $boardUser = boardUser::find($this->boardUserId);
       return view('admin.board.index')->with([
           'board_id'      => $boardUser->board_id,
        ]);
    }

    public function mount()
    {
       $boardUser = boardUser::find($this->boardUserId);
       $this->newNummer     =$boardUser->nummer;
       $this->newPostenemail=$boardUser->postenemail;
    }

    public function render()
    {
        $boardUser = boardUser::find($this->boardUserId);
        return view('livewire.board-user-edit')->with([
            'boardUserId'      => $this->boardUserId,
            'newNummer'        => $boardUser->nummer,
            'boardUser'        => $boardUser,
        ]);
    }
}
