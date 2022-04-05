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


    // ToDo: Validate funktioniert nichtm die Valedierung wird in @board-use-edit.blade.php überprüft
    /*
    protected $rules = [
        'newNummer' => 'required|min:6',
        'newPostenemail' => 'required|email',
    ];
    */
    /*
    public function updated($field)
    {
        $this->validateOnly($field, [
            'newNummer'      => 'min:1|max:2',
            'newPostenemail' => 'required|email',
            ]);
    }
*/

    public function updateNummer()
    {

       //$this->validateOnly($propertyName);

       if($this->newNummer==''){
           $this->newNummer=0;
       }

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
       $this->newNummer      = $boardUser->nummer;
       $this->newPostenemail = $boardUser->postenemail;
     }

    public function render()
    {
        $boardUser = boardUser::find($this->boardUserId);
        $board     = board::find($boardUser->board_id);
        return view('livewire.board-user-edit')->with([
            'boardUserId'      => $this->boardUserId,
            'newNummer'        => $boardUser->nummer,
            'boardUser'        => $boardUser,
            'board'            => $board,
        ]);
    }
}
