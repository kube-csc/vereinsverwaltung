<?php

namespace App\Http\Livewire;

use App\Models\board;
use App\Models\boardUser;
use App\Models\BoardPortrait;
use App\Models\User;
use Illuminate\Support\Carbon;
use Livewire\WithPagination;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class BoardUserMatch extends Component
{
    use WithPagination;

    public $boardUserId;
    public $searchUser;
    public $userSelected;
    public $image;
    public $savedImage;
    public $currentImage;
    public $deletionNote;

    protected $listeners = [
        'userSelected',
        'fileUpload'     => 'handleFileUpload',
        'deletionNote',
    ];

    public function userSelected($userId)
    {
        if ($userId==0)
            {
                $this->userSelected = Null;
                $this->currentImage = '';
                session()->flash('message', 'Die Mitgliederzuordnung zum Posten wurde gelöscht.');
            }
        else
            {
                $this->userSelected = $userId;
                $boardPortraits = boardPortrait::where('postenUser_id' , $userId)->get();
                foreach($boardPortraits as $boardPortrait) {
                    $this->currentImage = $boardPortrait->postenPortraet;
                }
                session()->flash('message', 'Die Mitglied wurde zum Posten zugeordnet.');
            }
    }

    public function handleFileUpload($imageData)
    {
        $this->image = $imageData;
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

        $imageName  = $this->saveInmage();
        if(isset($imageName)){
            $boardUser    = boardUser::find($this->boardUserId);
            $oldPortraits = BoardPortrait::where('postenUser_id' , $boardUser->boardUser_id)->get();

            if ($oldPortraits->count()==0){
                $boardPortrait = new BoardPortrait(
                    [
                        'postenUser_id'    => $boardUser->boardUser_id,
                        'postenPortraet'   => $imageName,
                        'bearbeiter_id'    => Auth::user()->id,
                        'user_id'          => Auth::user()->id,
                        'visible'          => 1,
                        'datumvon'         => Carbon::now(),
                        'datumbis'         => Carbon::now(),
                        'updated_at'       => Carbon::now(),
                        'created_at'       => Carbon::now()
                    ]
                );
                $boardPortrait->save();
            }
            else {
                foreach ($oldPortraits as $oldPortrait) {
                    if(file_exists(public_path().'/storage/boardPortrait/'.$oldPortrait->postenPortraet) and
                        $oldPortrait->postenPortraet != '' and
                        $oldPortrait->postenPortraet[6] != '-'){
                        unlink(public_path().'/storage/boardPortrait/'.$oldPortrait->postenPortraet);
                    }

                    BoardPortrait::find($oldPortrait->id)->update([
                        'postenPortraet'   => $imageName,
                        'bearbeiter_id'    => Auth::user()->id,
                        'updated_at'       => Carbon::now()
                    ]);
                }
            }
            $this->deletionNote=0; // es wird hier nicht zusätzlich eine Bildlöschung angestossen
        }

        $this->image = '';
        if($imageName) {
            $this->currentImage = $imageName;
        }

        if($this->deletionNote==1){
            $deletionNote = $this->currentImageDelete();
            $this->deletionNote=0;
        }

        session()->flash('message', 'Daten wurden gespeichert.');

        $boardUser = boardUser::find($this->boardUserId);
        return view('admin.board.index')->with([
            'board_id'      => $boardUser->board_id,
        ]);
    }

    public function saveInmage()
    {
        if (!$this->image) {
            return null;
        }

        $newImageName='posten_'.$this->boardUserId.'_'.str::random(4).'.jpg';

            Storage::disk('local')->putFileAs(
                'public/boardPortrait/',
                $this->image,
                $newImageName
            );

            return $newImageName;
    }

    public function deletionNote(){
      $this->deletionNote=1;
        session()->flash('message', 'Löschvermerk für das Porträt wurde gesetzt.');
    }

    public function currentImageDelete(){

        $boardUser    = boardUser::find($this->boardUserId);
        $oldPortraits = BoardPortrait::where('postenUser_id' , $boardUser->boardUser_id)->get();

        foreach($oldPortraits as $oldPortrait) {
            BoardPortrait::find($oldPortrait->id)->update([
                'postenPortraet' => Null,
                'bearbeiter_id'  => Auth::user()->id,
                'updated_at'     => Carbon::now()
            ]);

            if (file_exists(public_path() . '/storage/boardPortrait/' . $this->currentImage) and
                $this->currentImage != '' and
                $this->currentImage[6] != '-') {
                unlink(public_path() . '/storage/boardPortrait/' . $this->currentImage);
            }
        }
        $this->currentImage = '';
    }

    public function mount()
    {
        $boardUser      = boardUser::find($this->boardUserId);
        $boardPortraits = boardPortrait::where('postenUser_id' , $boardUser->boardUser_id)->get();
        foreach($boardPortraits as $boardPortrait){
            $this->currentImage = $boardPortrait->postenPortraet;
        }
        $this->userSelected = $boardUser->boardUser_id;
    }

    public function render()
    {
        $boardUser     = boardUser::find($this->boardUserId);
        $board         = board::find($boardUser->board_id);

        $users      = user::where('deleted_at' , NULL)
                          ->where('vorname' , 'LIKE' , '%'.$this->searchUser.'%')
                          ->orwhere('nachname' , 'LIKE' , '%'.$this->searchUser.'%')
                          ->paginate(10);

        return view('livewire.board-user-match')->with([
            'boardUserId'      => $this->boardUserId,
            'boardUser'        => $boardUser,
            'board'            => $board,
            'users'            => $users
        ]);
        return view('livewire.board-user-match');
    }
}
