<?php

namespace App\Http\Controllers;

use App\Models\instruction;
use Illuminate\Http\Request;

class InstructionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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

    public function datenschutz()
    {
        $instruction = instruction::find(1);
        $test=$instruction->beschreibung;
        return view('instruction.datenschutzerklaerung')->with(
            [
                'instruction'     => $instruction,
            ]
        );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\instruction  $instruction
     * @return \Illuminate\Http\Response
     */
    public function edit(instruction $instruction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\instruction  $instruction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, instruction $instruction)
    {
        //
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
