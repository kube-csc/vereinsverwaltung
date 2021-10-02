<?php

namespace App\Http\Controllers;

use App\Models\newBotmanQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class NewBotmanQuestionController extends Controller
{
    public function aktiv($newBotmanQuestionId)
    {
        newBotmanQuestion::find($newBotmanQuestionId)->update([
            'visible'      => '1',
            'updated_at'   => Carbon::now()
        ]);
        return Redirect()->back()->with('success' , 'Frage wurde sichtbar geschaltet.');
    }

    public function inaktiv($newBotmanQuestionId)
    {
        newBotmanQuestion::find($newBotmanQuestionId)->update([
            'visible'      => '0',
            'updated_at'   => Carbon::now()
        ]);
        return Redirect()->back()->with('success' , 'Frage wurde unsichtbar geschaltet.');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $newBotmanQuestions = newBotmanQuestion::all();
        return view('admin.newBotmanQuestion.index')->with(
            [
                'newBotmanQuestions' => $newBotmanQuestions,
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
     * @param  \App\Models\newBotmanQuestion  $newBotmanQuestion
     * @return \Illuminate\Http\Response
     */
    public function show(newBotmanQuestion $newBotmanQuestion)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\newBotmanQuestion  $newBotmanQuestion
     * @return \Illuminate\Http\Response
     */
    public function edit(newBotmanQuestion $newBotmanQuestion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\newBotmanQuestion  $newBotmanQuestion
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, newBotmanQuestion $newBotmanQuestion)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\newBotmanQuestion  $newBotmanQuestion
     * @return \Illuminate\Http\Response
     */
    public function destroy(newBotmanQuestion $newBotmanQuestion)
    {
        //
    }

    public function softDelete($newBotmanQuestionId)
    {
        $delete = newBotmanQuestion::find($newBotmanQuestionId)->delete();

        return redirect('/newBotmanQuestion/alle')->with(
            [
                'success' => 'Die Frage wurde gelöscht.'
            ]
        );
    }
}
