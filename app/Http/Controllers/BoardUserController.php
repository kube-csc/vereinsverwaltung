<?php

namespace App\Http\Controllers;

use App\Models\board;
use App\Models\boardUser;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class BoardUserController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function aktiv($boardId)
    {
        boardUser::find($boardId)->update([
            'visible'          => '1',
            'updated_at'       => Carbon::now()
        ]);
        return Redirect()->back()->with('successBoardUser' , 'Der Posten wurde sichtbar geschaltet.');
    }

    public function inaktiv($boardId)
    {
        boarduser::find($boardId)->update([
            'visible'          => '0',
            'updated_at'       => Carbon::now()
        ]);
        return Redirect()->back()->with('successBoardUser' , 'Der Posten wurde unsichtbar geschaltet.');
    }

    public function maxtop($boardUserId)
    {
        $boardUser = boardUser::find($boardUserId);
        $boardId=$boardUser->board_id;

        boardUser::find($boardUserId)->update([
            'position'         => '0',
            'updated_at'       => Carbon::now()
        ]);

        $boardUsers = boardUser::where('board_id',$boardId)
            ->orderby('position')
            ->get();
        $positionNew=10;
        foreach ($boardUsers as $boardUser){
            boardUser::find($boardUser->id)->update([
                'position'         => $positionNew,
                'updated_at'       => Carbon::now()
            ]);
            $positionNew=$positionNew+10;
        }
        return Redirect()->back()->with('successBoardUser' , 'Der Posten wurde zur Top Position verschoben.');
    }

    public function top($boardUserId)
    {
        // ToDo verebessern der Updatefunktion
        $boardUser = boardUser::find($boardUserId);
        $boardId=$boardUser->board_id;

        $boardUser = boardUser::find($boardUserId);
        $positionNew=$boardUser->position-11;

        boardUser::find($boardUserId)->update([
            'position'      => $positionNew,
            'updated_at'    => Carbon::now()
        ]);

        $positionNew=10;
        $boardUsers = boardUser::where('board_id',$boardId)
            ->orderby('position')
            ->get();
        foreach ($boardUsers as $boardUser){
            boardUser::find($boardUser->id)->update([
                'position' => $positionNew,
                'updated_at'       => Carbon::now()
            ]);
            $positionNew=$positionNew+10;
        }
        return Redirect()->back()->with('successBoardUser' , 'Der Posten wurde eine Position nach oben verschoben.');
    }

    public function down($boardUserId)
    {
        // ToDo verebessern der Updatefunktion
        $boardUser = boardUser::find($boardUserId);
        $boardId=$boardUser->board_id;

        $boardUser = boardUser::find($boardUserId);
        $positionNew=$boardUser->position+11;
        boardUser::find($boardUserId)->update([
            'position'         => $positionNew,
            'updated_at'       => Carbon::now()
        ]);

        $boardUsers = boardUser::where('board_id',$boardId)->orderby('position')->get();
        $positionNew=10;
        foreach ($boardUsers as $boardUser){
            boardUser::find($boardUser->id)->update([
                'position'         => $positionNew,
                'updated_at'       => Carbon::now()
            ]);
            $positionNew=$positionNew+10;
        }
        return Redirect()->back()->with('successBoardUser' , 'Der Posten wurde eine Position nach unten verschoben.');
    }

    public function maxdown($boardUserId)
    {
        $boardUser = boardUser::find($boardUserId);
        $boardId=$boardUser->board_id;

        $boardUsers = boardUser::where('board_id',$boardId)
            ->orderby('position' , 'desc')
            ->limit(1)
            ->get();
        foreach ($boardUsers as $boardUser){
            $positionNew=$boardUser->position+10;
        }

        boardUser::find($boardUserId)->update([
            'position'      => $positionNew,
            'updated_at'    => Carbon::now()
        ]);

        $boardUsers = boardUser::where('board_id',$boardId)->orderby('position')->get();
        $positionNew=10;
        foreach ($boardUsers as $boardUser){
            boardUser::find($boardUser->id)->update([
                'position'         => $positionNew,
                'updated_at'       => Carbon::now()
            ]);
            $positionNew=$positionNew+10;
        }
        return Redirect()->back()->with('successBoardUser' , 'Der Posten wurde zur letzten Position verschoben.');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($board_id)
    {
        $boards         = board::orderby('position')->paginate(5);
        $boardMaxs      = board::orderby('position')->get();
        if($boardMaxs->count()>0){
            $boardMaxID     = $boardMaxs->last()->id;
        }
        else{
            $boardMaxID=0;
        }

        $boardName      = board::find($board_id);
        $boardUsers     = boardUser::where('board_id' , $board_id)->orderby('position')->get();
        if($boardUsers->count()>0){
            $boardUserMaxID = $boardUsers->last()->id;
        }
        else{
            $boardUserMaxID=0;
        }

        return view('admin.boardUser.index')->with(
            [
                'boards'         => $boards,
                'boardMaxID'     => $boardMaxID,
                'boardUsers'     => $boardUsers,
                'boardUserMaxID' => $boardUserMaxID,
                'boardIdSelecte' => $board_id,
                'boardUserName'  => $boardName->postenMaenlich." / ".$boardName->postenWeiblich,
            ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($boardId)
    {
        $boardUsers = boardUser::where('board_id',$boardId)
            ->orderby('position' , 'desc')
            ->limit(1)
            ->get();
        foreach ($boardUsers as $boardUser){
            $positionUserNew=$boardUser->position+10;
        }
        if(!isset($positionUserNew)){
            $positionUserNew=10;
        }

        $boardUser= new boardUser(
            [
                'board_id'         => $boardId,
                'user_id'          => Auth::user()->id,
                'bearbeiter_id'    => Auth::user()->id,
                'visible'          => 1,
                'position'         => $positionUserNew,
                'updated_at'       => Carbon::now(),
                'created_at'       => Carbon::now()
            ]
        );
        $boardUser->save();

        return redirect('/Posten/'.$boardId)->with(
            [
                'successBoardUser' => 'Ein Posten wurden angelegt.'
            ]
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($boardUserId)
    {
        return view('admin.boardUser.edit')->with(
        [
            'boardUserId'=> $boardUserId,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($boardUser_id)
    {
        /*
        ToDo: Rechte abfrage auf ergänzen
        if (auth()->guest()){
            abort(403);
        }

        abort_unless(Gate::allows('delete' , $hobby) , 403);
        */

        $boardUser = boardUser::find($boardUser_id);
        $boardUser->delete();

        return back()->with([
            'successBoardUser' => 'Ein Posten wurden gelöscht.'
        ]);
    }

    public function match($boardUserId)
    {
        return view('admin.boardUser.editUser')->with(
            [
                'boardUserId'=> $boardUserId,
            ]);
    }

}
