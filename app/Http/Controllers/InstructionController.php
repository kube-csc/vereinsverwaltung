<?php

namespace App\Http\Controllers;

use App\Models\instruction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class InstructionController extends Controller
{

    public function aktiv($instruction_id)
    {
        //dd('aktiv');
        instruction::find($instruction_id)->update([
            'visible'      => '1',
            'updated_at'  => Carbon::now()
        ]);

        return Redirect()->back()->with('success' , 'Informationsseite wurde sichtbar geschaltet.');
    }

    public function inaktiv($instruction_id)
    {
        //dd('inaktiv') ;
        instruction::find($instruction_id)->update([
            'visible'      => '0',
            'updated_at'  => Carbon::now()
        ]);

        return Redirect()->back()->with('success' , 'Informationsseite wurde unsichtbar geschaltet.');
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $instructions = instruction::orderby('ueberschrift')->get();

        return view('admin.instruction.index')->with(
            [
                'instructions'    => $instructions
            ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
     * @param  \App\Models\instruction  $instruction
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
      //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\instruction  $instruction
     * @return \Illuminate\Http\Response
     */
    public function edit($instruction_id)
    {
        $instruction =instruction::find($instruction_id);

        return view('admin.instruction.edit',compact('instruction'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\instruction  $instruction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $instruction_id)
    {
        instruction::find($instruction_id)->update([
            'beschreibung'    => $request->beschreibung,
            'updated_at'      => Carbon::now()
        ]);

        return redirect('/Instruction/alle')->with(
            [
                'success' => 'Die Daten wurden geändert.'
            ]
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\instruction  $instruction
     * @return \Illuminate\Http\Response
     */
    public function destroy(instruction $instruction)
    {
        //
    }

}
