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

    public function cronPlanung()
    {
        $currentDate = Carbon::now();
        $trainings = Training::all();

        foreach ($trainings as $training) {
            $courseDates = Coursedate::where('training_id', $training->id)->get();

            $newDate             = Carbon::parse($training->datumvon);
            $kurslaenge          = Carbon::parse($training->zeitbis)->diff(Carbon::parse($training->zeitvon))->format('%H:%I:%S');
            $wiederholungAktuell = 0;
            $datumberechnug = max(Carbon::parse($training->datumAktuell), Carbon::now());

            while ($newDate < $datumberechnug) {
                $newDate->addDays($training->wiederholung);
            }

            foreach ($courseDates as $courseDate) {
                if (Carbon::parse($courseDate->kursstartvorschlag) < Carbon::now()) {
                    $datumvon = Carbon::parse($newDate)->addSeconds(Carbon::parse($training->zeitvon)->diffInSeconds(Carbon::parse('00:00:00')));
                    $datumbis = Carbon::parse($newDate)->addSeconds(Carbon::parse($training->zeitbis)->diffInSeconds(Carbon::parse('00:00:00')));

                    if($wiederholungAktuell >= $training->vorschauTage){
                        break;
                    }

                    $courseDateTest = Coursedate::where('kursstartvorschlag' ,$datumvon)
                        ->where('training_id' ,$courseDate->training_id)
                        ->get();

                    if($courseDateTest->isNotEmpty()) {
                        $courseDate->update([
                            'course_id' => $training->course_id,

                            'kursstarttermin' => $datumvon,
                            'kursendtermin' => $datumbis,
                            'kursstartvorschlag' => $datumvon,
                            'kursendvorschlag' => $datumbis,
                            'kursstartvorschlagkunde' => $datumvon,
                            'kursendvorschlagkunde' => $datumbis,
                            'kurslaenge' => $kurslaenge,
                            'sportgeraetanzahl' => $training->sportgeraetanzahl,
                            'sportgeraeteGebucht' => $training->sportgeraeteGebucht,

                            'bearbeiter_id' => $training->autor_id,
                            'updated_at' => Carbon::now(),
                        ]);
                    }
                }

                $newDate->addDays($training->wiederholung);
                $wiederholungAktuell = $wiederholungAktuell+$training->wiederholung;
            }

            while ($newDate <= Carbon::parse($training->datumbis)) {
                if($wiederholungAktuell >= $training->vorschauTage){
                    break;
                }

                $datumvon = Carbon::parse($newDate)->addSeconds(Carbon::parse($training->zeitvon)->diffInSeconds(Carbon::parse('00:00:00')));
                $datumbis = Carbon::parse($newDate)->addSeconds(Carbon::parse($training->zeitbis)->diffInSeconds(Carbon::parse('00:00:00')));

                if($courseDates->count() == 0) {
                    $courseDateDelete = Coursedate::withTrashed()
                        ->where('training_id', '>', 0)
                        ->where('deleted_at' , '!=', null)
                        ->first();

                    if ($courseDateDelete) {
                        $courseDateDelete->restore();
                        $courseDateDelete->update([
                            'course_id' => $training->course_id,

                            'kursstarttermin' => $datumvon,
                            'kursendtermin' => $datumbis,
                            'kursstartvorschlag' => $datumvon,
                            'kursendvorschlag' => $datumbis,
                            'kursstartvorschlagkunde' => $datumvon,
                            'kursendvorschlagkunde' => $datumbis,
                            'kurslaenge' => $kurslaenge,
                            'sportgeraetanzahl' => $training->sportgeraeteanzahl,
                            'sportgeraeteGebucht' => $training->sportgeraeteGebucht,

                            'bearbeiter_id' => $training->bearbeiter_id,
                            'autor_id' => $training->bearbeiter_id,

                            'updated_at' => Carbon::now(),
                            'created_at' => Carbon::now()
                        ]);
                    }
                    else{
                        Coursedate::create([
                            'organiser_id' => $training->organiser_id,
                            'training_id' => $training->id,
                            'course_id' => $training->course_id,

                            'kursstarttermin' => $datumvon,
                            'kursendtermin' => $datumbis,
                            'kursstartvorschlag' => $datumvon,
                            'kursendvorschlag' => $datumbis,
                            'kursstartvorschlagkunde' => $datumvon,
                            'kursendvorschlagkunde' => $datumbis,
                            'kurslaenge' => $kurslaenge,
                            'sportgeraetanzahl' => $training->sportgeraeteanzahl,
                            'sportgeraeteGebucht' => $training->sportgeraeteGebucht,

                            'bearbeiter_id' => $training->bearbeiter_id,
                            'autor_id' => $training->bearbeiter_id,
                            'updated_at' => Carbon::now(),
                            'created_at' => Carbon::now()
                        ]);
                    }
                }
                else {
                    $courseDateTest = Coursedate::where('kursstartvorschlag', $datumvon)
                        ->where('training_id', $courseDate->training_id)
                        ->get();

                    if ($courseDateTest->isNotEmpty()) {
                        Coursedate::create([
                            'organiser_id' => $training->organiser_id,
                            'training_id' => $training->id,
                            'course_id' => $training->course_id,

                            'kursstarttermin' => $datumvon,
                            'kursendtermin' => $datumbis,
                            'kursstartvorschlag' => $datumvon,
                            'kursendvorschlag' => $datumbis,
                            'kursstartvorschlagkunde' => $datumvon,
                            'kursendvorschlagkunde' => $datumbis,
                            'kurslaenge' => $kurslaenge,
                            'sportgeraetanzahl' => $training->sportgeraetanzahl,
                            'sportgeraeteGebucht' => $training->sportgeraeteGebucht,

                            'bearbeiter_id' => $training->bearbeiter_id,
                            'autor_id' => $training->bearbeiter_id,
                            'updated_at' => Carbon::now(),
                            'created_at' => Carbon::now()
                        ]);
                    }
                }
                $newDate->addDays($training->wiederholung);
                $wiederholungAktuell = $wiederholungAktuell+$training->wiederholung;
            }

            $courseLetzte = Coursedate::where('training_id', $training->id)
                ->orderBy('kursstartvorschlag', 'desc')
                ->first();

            $training->update([
                'datumAktuell' => $courseLetzte->kursstartvorschlag,
            ]);
        }
    }
}
