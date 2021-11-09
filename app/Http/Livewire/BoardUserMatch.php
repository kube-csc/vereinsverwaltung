<?php

namespace App\Http\Livewire;

use App\Models\boardUser;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class BoardUserMatch extends Component
{
    public $boardUserId;
    public $searchUser;
    public $userSelected;

    protected $listeners = ['userSelected'];

    public function userSelected($userId)
    {
        if ($userId==0)
            {
                $this->userSelected = Null;
            }
        else
            {
                $this->userSelected = $userId;
            }
    }

    public function updated($field)
    {
        /*
        $this->validateOnly($field, [
            'newNummer'      => 'required|max:2',
        ]);
        */
    }

    public function updateBoardUser()
    {
        /*
        $this->validate([
            'newNummer'      => 'required|max:2',
        ]);
        */
        boardUser::find($this->boardUserId)->update([
            'boardUser_id'     => $this->userSelected,
            'bearbeiter_id'    => Auth::user()->id,
            'updated_at'       => Carbon::now()
        ]);

        session()->flash('message', 'Daten wurden gespeichert.');

        $boardUser = boardUser::find($this->boardUserId);
        return view('admin.board.index')->with([
            'board_id'      => $boardUser->board_id,
        ]);
    }

    public function mount()
    {
        $boardUser  = boardUser::find($this->boardUserId);
        $this->userSelected = $boardUser->boardUser_id;
    }

    public function render()
    {
        $boardUser  = boardUser::find($this->boardUserId);
        $users      = user::where('deleted_at' , NULL)
                            ->where('vorname' , 'LIKE' , '%'.$this->searchUser.'%')
                            ->orwhere('nachname' , 'LIKE' , '%'.$this->searchUser.'%')
                            ->get();


        return view('livewire.board-user-match')->with([
            'boardUserId'      => $this->boardUserId,
            'boardUser'        => $boardUser,
            'users'            => $users
        ]);
        return view('livewire.board-user-match');
    }
}
