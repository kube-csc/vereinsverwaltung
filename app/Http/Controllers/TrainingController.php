<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Coursedate;
use App\Models\SportSection;
use App\Models\Training;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrainingController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($sportSectionId)
    {
        $sportSection = SportSection::find($sportSectionId);
        $trainings    = Training::where('sportSection_id', $sportSectionId)->paginate(10);

        return view('admin.training.index', compact('trainings', 'sportSection'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($sportSectionId)
    {
        $courses = Course::join('course_sport_section', 'courses.id', '=', 'course_sport_section.course_id')
            ->where('course_sport_section.sport_section_id', $sportSectionId)
            ->get();

        return view('admin.training.create', compact('sportSectionId', 'courses'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'datumvon' => 'required|date',
            'datumbis' => 'required|date',
            'zeitvon' => 'required',
            'zeitbis' => 'required',
            'courseId' => 'required|integer|min:1',
            'sportgeraeteanzahl' => 'integer|min:1|max:999|gte:sportgeraeteGebucht',
            'sportgeraeteReserviert' => 'integer|min:1|max:999|lte:sportgeraeteanzahl',
            'vorschauTage' => 'integer|min:1|max:400',
            'wiederholung' => 'integer|min:1|max:365',
        ]);

        $course   = Course::find($request->get('courseId'));
        $training = new Training(
            [
                'sportSection_id'   => $request->get('sportSection_id'),
                'course_id'         => $request->get('courseId'),
                'organiser_id'      => $course->organiser_id,
                'datumvon'          => $request->get('datumvon'),
                'datumbis'          => $request->get('datumbis'),
                'datumAktuell'      => $request->get('datumvon'),
                'zeitvon'           => $request->get('zeitvon'),
                'zeitbis'           => $request->get('zeitbis'),
                'vorschauTage'      => $request->get('vorschauTage'),
                'wiederholung'      => $request->get('wiederholung'),
                'sportgeraeteanzahl'=> $request->get('sportgeraeteanzahl'),
                'sportgeraeteReserviert' => $request->get('sportgeraeteReserviert'),
                'bearbeiter_id'     => Auth::id(),
                'autor_id'          => Auth::id(),
                'updated_at'        => Carbon::now(),
                'created_at'        => Carbon::now()
            ]
        );
        $training->save();

        return redirect()->route('training.index', ['sportSection_id' => $request->sportSection_id])->with('success', 'Das Training wurde erfolgreich erstellt.');
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
        $training = Training::find($id);
        $courses = Course::join('course_sport_section', 'courses.id', '=', 'course_sport_section.course_id')
            ->where('course_sport_section.sport_section_id', $training->sportSection_id)
            ->get();

        return view('admin.training.edit', compact('training', 'courses'));
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
       $request->validate([
            'datumvon' => 'required|date',
            'datumbis' => 'required|date',
            //'zeitvon' => 'required|date_format:H:i:s',
            //'zeitbis' => 'required|date_format:H:i:s',
            'sportgeraeteanzahl' => 'integer|min:1|max:999|gte:sportgeraeteGebucht',
            'sportgeraeteReserviert' => 'integer|min:1|max:999|lte:sportgeraeteanzahl',
            'sportSection_id' => 'required|integer',
            'vorschauTage' => 'required|string',
            'wiederholung' => 'required|string',
       ]);

        $request['datumAktuell'] = $request->get('datumvon');

        $training = Training::find($id);
        $training->update($request->all());

        return redirect()->route('training.index', ['sportSection_id' => $request->sportSection_id])->with('success', 'Das Training erfolgreich wurde aktualisiert.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Coursedate::where('training_id', $id)->delete();
        $training = Training::find($id);
        $sportSectionId = $training->sportSection_id;
        $training->delete();

        return redirect()->route('training.index', ['sportSection_id' => $sportSectionId])->with('success', 'Das Training wurde erfolgreich gelöscht.');
    }
}
