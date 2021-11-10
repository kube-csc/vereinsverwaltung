<?php

namespace App\Http\Controllers;

use App\Models\board;
use App\Models\boardUser;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class BoardController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function boardSelected($board_id)
    {
        $this->active = $board_id;
    }

    public function aktiv($boardId)
    {
        board::find($boardId)->update([
            'visible'          => '1',
            'bearbeiter_id'    => Auth::user()->id,
            'updated_at'       => Carbon::now()
        ]);
        return Redirect()->back()->with('success' , 'Posten wurde sichtbar geschaltet.');
    }

    public function inaktiv($boardId)
    {
        board::find($boardId)->update([
            'visible'          => '0',
            'bearbeiter_id'    => Auth::user()->id,
            'updated_at'       => Carbon::now()
        ]);
        return Redirect()->back()->with('success' , 'Posten wurde unsichtbar geschaltet.');
    }

    public function maxtop($boardId)
    {
        board::find($boardId)->update([
            'position'         => '0',
            'bearbeiter_id'    => Auth::user()->id,
            'updated_at'       => Carbon::now()
        ]);

        // ToDo verebessern der Updatefunktion
        //board::all()->update(['position' => 'position']);

        $boards = board::orderby('position')->get();
        $positionNew=10;
        foreach ($boards as $board){
           board::find($board->id)->update([
               'position'         => $positionNew
            ]);
            $positionNew=$positionNew+10;
        }
        return Redirect()->back()->with('success' , 'Posten wurde zur Top Position verschoben.');
    }

    public function top($boardId)
    {
        // ToDo verebessern der Updatefunktion
        $board = board::find($boardId);
        $positionNew=$board->position-11;

        board::find($boardId)->update([
            'position'      => $positionNew,
            'bearbeiter_id'    => Auth::user()->id,
            'updated_at'       => Carbon::now()
        ]);

        $positionNew=10;
        $boards = board::orderby('position')->get();
        foreach ($boards as $board){
             board::find($board->id)->update([
                'position'      => $positionNew
            ]);
            $positionNew=$positionNew+10;
        }
        return Redirect()->back()->with('success' , 'Der Posten wurde eine Position nach oben verschoben.');
    }

    public function down($boardId)
    {
        // ToDo verebessern der Updatefunktion
        $board = board::find($boardId);
        $positionNew=$board->position+11;
        board::find($boardId)->update([
            'position'         => $positionNew,
            'bearbeiter_id'    => Auth::user()->id,
            'updated_at'       => Carbon::now()
        ]);

        $boards = board::orderby('position')->get();
        $positionNew=10;
        foreach ($boards as $board){
            // $boardId=board::find($board->id);
            board::find($board->id)->update([
                'position'      => $positionNew
            ]);
            $positionNew=$positionNew+10;
        }
        return Redirect()->back()->with('success' , 'Posten wurde eine Position nach unten verschoben.');
    }

    public function maxdown($boardId)
    {
        $boards = board::orderby('position' , 'desc')->limit(1)->get();
        foreach ($boards as $board){
            $positionNew=$board->position+10;
        }

        board::find($boardId)->update([
            'position'      => $positionNew,
            'bearbeiter_id'    => Auth::user()->id,
            'updated_at'       => Carbon::now()
        ]);

        $boards = board::orderby('position')->get();
        $positionNew=10;
        foreach ($boards as $board){
            board::find($board->id)->update([
                'position'      => $positionNew,
            ]);
            $positionNew=$positionNew+10;
        }
        return Redirect()->back()->with('success' , 'Posten wurde zur letzten Position verschoben.');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function boardBoardUser($board_id)
    {
        $boards = board::orderby('position')->paginate(5);
        $boardName= board::find($board_id);
        $boardUsers = boardUser::where('board_id' , $board_id)->orderby('position')->paginate(5);
        return view('admin.boardUser.index')->with(
            [
                'boards'         => $boards,
                'boardUsers'     => $boardUsers,
                'boardIdSelecte' => $board_id,
                'boardUserName'  => $boardName->postenmaenlich." / ".$boardName->postenweiblich,
            ]);
    }

    public function index()
    {
        $boards = board::orderby('position')->paginate(5);
        return view('admin.board.index')->with(
            [
                'boards' => $boards,
            ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.board.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate(
            [
                'postenmaenlich'         => 'required',
                'postenweiblich'         => 'required'
            ]
        );

        $boards = board::orderby('position' , 'desc')->limit(1)->get();
        foreach ($boards as $board){
            $positionNew=$board->position+10;
        }

        $board= new board(
            [
                'postenmaenlich'   => $request->postenmaenlich,
                'postenweiblich'   => $request->postenweiblich,
                'visible'          => 1,
                'position'         => $positionNew,
                'sportSection_id'  => 1,
                'bearbeiter_id'    => Auth::user()->id,
                'user_id'          => Auth::user()->id,
                'updated_at'       => Carbon::now(),
                'created_at'       => Carbon::now()
            ]
        );
        $board->save();

        return redirect('/Team/alle')->with(
            [
                'success' => 'Die Posten <b>' . $request->postenmaenlich.' / '.$request->postenweiblich.' wurden angelegt.'
            ]
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\board  $board
     * @return \Illuminate\Http\Response
     */
    public function show(board $board)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\board  $board
     * @return \Illuminate\Http\Response
     */
    public function edit($board_id)
    {
        $board = board::find($board_id);

        return view('admin.board.edit',compact('board' ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\board  $board
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $board_id)
    {
        $request->validate(
            [
                'postenmaenlich'         => 'required',
                'postenweiblich'         => 'required'
            ]
        );

        board::find($board_id)->update([
            'postenmaenlich'    => $request->postenmaenlich,
            'postenweiblich'    => $request->postenweiblich,
            'visible'           =>   '1',
            'updated_at'        => Carbon::now()
        ]);

        return redirect('/Team/alle')->with(
            [
                'success' => 'Die Daten von der Team <b>' . $request->postenmaenlich.' / '.$request->postenweiblich.'.'
            ]
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\board  $board
     * @return \Illuminate\Http\Response
     */
    public function destroy(board $board)
    {
        //
    }
}
