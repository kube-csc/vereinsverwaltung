<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Training;
use App\Models\Coursedate;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CoursedateController extends Controller
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
        $organiser = $this->organiser();

        $kursstartterminDatum = Carbon::now()->format('Y-m-d');
        $kursstartterminTime = Carbon::now()->format('H:i');
        $kurslaengeStunde = '01';
        $kurslaengeMinute = '30';
        $kurslaenge = $kurslaengeStunde.':'.$kurslaengeMinute;
        $kursendterminDatum=$kursstartterminDatum;
        $kursendterminTime = Carbon::now()->addHours($kurslaengeStunde)->addMinutes($kurslaengeMinute)->format('H:i');

        $trainer = Trainertable::where('user_id', Auth::user()->id)
            ->where('organiser_id', $organiser->id)
            ->get();

        if($trainer->count()>0){
            $courses = Course::where('organiser_id', $organiser->id)
                ->orderBy('kursName')
                ->get();
        }
        else{
            $courses = Course::where('organiser_id', $organiser->id)
                ->where('trainer', 0)
                ->orderBy('kursName')
                ->get();
        }

        if($courses->count()==0){
            self::warning('Es kann kein Kein Kurs / Fahrt angelegt werden, weil es hierfür keine Vorlage angelegt wurde.');

            return redirect()->back();
        }

        $course_id = 0;
        $sportgeraetanzahl = 0;
        $sportgeraetanzahlMax = $this->sportgeraetanzahlMax($organiser->id);

        return view('components.backend.courseDate.create' , compact([
            'kursstartterminDatum',
            'kursstartterminTime',
            'kurslaenge',
            'kursendterminDatum',
            'kursendterminTime',
            'sportgeraetanzahlMax',
            'sportgeraetanzahl',
            'courses',
            'course_id',
            'organiser'
        ]));
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
    public function edit($id)
    {
        //
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
    public function destroy($id)
    {
        //
    }

}
