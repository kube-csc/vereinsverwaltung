<?php

namespace App\Http\Controllers;

use App\Models\backlinks;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class BacklinksController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function aktiv($backlinksId)
    {
        backlinks::find($backlinksId)->update([
            'visible'          => '1',
            'bearbeiter_id'    => Auth::user()->id,
            'updated_at'       => Carbon::now()
        ]);
        return Redirect()->back()->with('success' , 'Der Backlink wurde sichtbar geschaltet.');
    }

    public function inaktiv($backlinksId)
    {
        backlinks::find($backlinksId)->update([
            'visible'          => '0',
            'bearbeiter_id'    => Auth::user()->id,
            'updated_at'       => Carbon::now()
        ]);
        return Redirect()->back()->with('success' , 'Der Backlink  wurde unsichtbar geschaltet.');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $backlinks = backlinks::where('neueUrl' , Null)
            ->orderby('created_at' , 'DESC')
            ->orderby('backlink')->paginate(5);
        return view('admin.backlink.index')->with(
            [
                'backlinks' => $backlinks,
            ]);
    }

    public function indexRelevant()
    {
        $backlinks = backlinks::where('neueUrl' , Null)
            ->orderby('nichtgefundenAnzahl' , 'DESC')
            ->orderby('backlink')
            ->paginate(5);

        return view('admin.backlink.indexRelevant')->with(
            [
                'backlinks' => $backlinks,
            ]);
    }

    public function indexUsed()
    {
        $i=0;
        $backlinks = backlinks::where(function ($query) use ($i){
                                    $query->where('neueUrl' , '!=', Null)
                                          ->orwhere('prefixName' , '!=' , NULL);
                                    })
                               ->orderby('weiterleitAnzahl' , 'DESC')
                               ->orderby('backlink')
                               ->paginate(5);

        return view('admin.backlink.indexRelevantUsed')->with(
            [
                'backlinks' => $backlinks,
            ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.backlink.create');
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
                'backlink' => 'required|max:255',
                'neueUrl'  => 'max:255'
            ]
        );

        $backlink = new backlinks(
            [
                'ip'               => 0,
                'backlink'         => $request->backlink,
                'neueUrl'          => $request->neueUrl,
                'bearbeiter_id'    => Auth::user()->id,
                'user_id'          => Auth::user()->id,
                'updated_at'       => Carbon::now(),
                'created_at'       => Carbon::now()
            ]
        );
        $backlink->save();

        return redirect('/Backlink/alle')->with(
            [
                'success' => 'Der Backlink <b>' . $request->backlink . '</b> wurde angelegt.'
            ]
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\backlinks  $backlinks
     * @return \Illuminate\Http\Response
     */
    public function show(backlinks $backlinks)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\backlinks  $backlinks
     * @return \Illuminate\Http\Response
     */
    public function edit($backlink_id)
    {
        $backlink = backlinks::find($backlink_id);
        return view('admin.backlink.edit',compact('backlink'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\backlinks  $backlinks
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $backlink_id)
    {
        $request->validate(
            [
                'backlink'    => 'required|max:255',
                'neueUrl'     => 'max:255',
                'prefixName'  => 'max:255',
                'prefixNummer'=> 'min:0|nax:10'
            ]
        );

        backlinks::find($backlink_id)->update([
            'backlink'         => $request->backlink,
            'neueUrl'          => $request->neueUrl,
            'teilUrl'          => $request->teilUrl,
            'prefixNummer'     => $request->prefixNumber,
            'prefixName'       => $request->prefixName,
            'bearbeiter_id'    => Auth::user()->id,
            'updated_at'       => Carbon::now()
        ]);

        return redirect('/Backlink/alle')->with(
            [
              'success' => 'Die Daten von Backlink<b>' . $request->backlink. '</b> wurden geändert.'
            ]
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\backlinks  $backlinks
     * @return \Illuminate\Http\Response
     */
    public function destroy(backlinks $backlinks)
    {
        //
    }

    public function softDelete($backlink_id)
    {
        $delete = backlinks::find($backlink_id)->delete();
        return redirect('/Backlink/alle')->with(
            [
                'success' => 'Der Backlink wurde gelöscht.'
            ]
        );
    }
}
