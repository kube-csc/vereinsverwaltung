<?php

namespace App\Http\Controllers;

use App\Models\instruction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class InstructionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function aktiv($instruction_id)
    {
        instruction::find($instruction_id)->update([
            'visible' => '1',
            'bearbeiter_id' => Auth::id(),
            'updated_at' => Carbon::now()
        ]);
        return Redirect()->back()->with('success', 'Informationsseite wurde sichtbar geschaltet.');
    }

    public function inaktiv($instruction_id)
    {
        $instruction = instruction::find($instruction_id);
        if ($instruction->hauptmenu == 0) {
            instruction::find($instruction_id)->update([
                'visible' => '0',
                'bearbeiter_id' => Auth::id(),
                'updated_at' => Carbon::now()
            ]);
        } else {
            instruction::where('hauptmenuspalte', $instruction->hauptmenuspalte)
                ->where('visible', '1')
                ->update([
                    'visible' => '0',
                    'bearbeiter_id' => Auth::id(),
                    'updated_at' => Carbon::now()
                ]);
        }

        return Redirect()->back()->with('success', 'Die Informationsseite wurde unsichtbar geschaltet.');
    }

    public function maxtop($instructionId)
    {
        $instruction = instruction::find($instructionId);
        $positionFilter = $instruction->hauptmenuspalte;
        if ($instruction->hauptmenu == 2 && $instruction->hauptmenuspalte == 10 ) {

            $this->menulevel1($instructionId , $positionFilter);

        }
        else {
            instruction::find($instructionId)->update([
                'position' => '0',
                'bearbeiter_id' => Auth::id(),
                'updated_at' => Carbon::now()
            ]);

            $instructions = instruction::orderby('position')->get();
            $positionNew = 10;
            foreach ($instructions as $instruction) {
                instruction::find($instruction->id)->update([
                    'position' => $positionNew
                ]);
                $positionNew = $positionNew + 10;
            }
        }
        return Redirect()->back()->with('success', 'Die Informationsseite wurde zur Top Position verschoben.');
    }

    public function top($instructionId)
    {
        $instruction = instruction::find($instructionId);
        $positionFilter = $instruction->hauptmenuspalte;
        if ($instruction->hauptmenu == 2 && $instruction->hauptmenuspalte == 10 ) {

            $this->menulevel1($instructionId , $positionFilter);

        }
        else{
            $positionNew = $instruction->position - 11;

            instruction::find($instructionId)->update([
                'position' => $positionNew,
                'bearbeiter_id' => Auth::id(),
                'updated_at' => Carbon::now()
            ]);

            $positionNew = 10;
            $instructions = instruction::orderby('position')->get();
            foreach ($instructions as $instruction) {
                instruction::find($instruction->id)->update([
                    'position' => $positionNew
                ]);
                $positionNew = $positionNew + 10;
            }
        }
        return Redirect()->back()->with('success', 'Die Informationsseite wurde eine Position nach oben verschoben.');
    }

    public function down($instructionId)
    {
        $instruction = instruction::find($instructionId);
        $positionNew = $instruction->position + 11;
        $menulevel = $instruction->hauptmenuspalte;
        instruction::find($instructionId)->update([
            'position' => $positionNew,
            'bearbeiter_id' => Auth::id(),
            'updated_at' => Carbon::now()
        ]);

        $instructions = instruction::where('hauptmenuspalte', $menulevel)
            ->orderby('position')
            ->get();
        $positionNew = 10;
        foreach ($instructions as $instruction) {
             instruction::find($instruction->id)->update([
                'position' => $positionNew
            ]);
            $positionNew = $positionNew + 10;
        }
        return Redirect()->back()->with('success', 'Die Informationsseite wurde eine Position nach unten verschoben.');
    }

    public function maxdown($instructionId)
    {
        $positionNew=0;
        $instructions = instruction::orderby('position', 'desc')
                                   ->limit(1)
                                   ->get();
        foreach ($instructions as $instruction) {
            $positionNew = $instruction->position + 10;
        }

        instruction::find($instructionId)->update([
            'position' => $positionNew,
            'bearbeiter_id' => Auth::id(),
            'updated_at' => Carbon::now()
        ]);

        $instructions = instruction::orderby('position')->get();
        $positionNew = 10;
        foreach ($instructions as $instruction) {
            instruction::find($instruction->id)->update([
                'position' => $positionNew,
            ]);
            $positionNew = $positionNew + 10;
        }
        return Redirect()->back()->with('success', 'Die Informationsseite wurde zur letzten Position verschoben.');
    }

    public function menuNew($instructionId)
    {
        $instructions = instruction::orderby('hauptmenuspalte')
                                   ->orderby('position')
                                   ->get();
        $first=0;
        $firstPosition=0;
        $hauptmenuspalteOld=0;
        $hauptmenuspalteNew=10;
        foreach ($instructions as $instruction) {
             if($instruction->hauptmenuspalte>$hauptmenuspalteOld)
             {
                 $hauptmenuspalteNew=$hauptmenuspalteNew+10;
                 if($first==1) {
                     $firstPosition=1;
                 }
             }
             $hauptmenuspalteOld=$instruction->hauptmenuspalte;

             if($first==1){
                 instruction::find($instruction->id)->update([
                     'hauptmenuspalte' => $hauptmenuspalteNew,
                     'bearbeiter_id'   => Auth::id(),
                     'updated_at'      => Carbon::now()
                 ]);
                 if($firstPosition==0) {
                     instruction::find($instruction->id)->update([
                         'position'  => 10,
                         'hauptmenu' => 1
                     ]);
                     $hauptmenuspalteNew=$hauptmenuspalteNew+10;
                 }
             }

            if($instructionId==$instruction->id) {
                $first=1;
                if($instruction->hauptmenu==1){
                    $hauptmenuspalteNew=$instruction->hauptmenuspalte;
                }
                else{
                    $hauptmenuspalteNew=$instruction->hauptmenuspalte+10;
                }

                instruction::find($instruction->id)->update([
                    'hauptmenu'       => 2,
                    'hauptmenuspalte' => $hauptmenuspalteNew,
                    'position'        => 0,
                    'bearbeiter_id'   => Auth::id(),
                    'updated_at'      => Carbon::now()
                ]);
                $hauptmenuspalteNew=$hauptmenuspalteNew+10;
            }
        }
        return Redirect()->back()->with('success', 'Zu ein Menu-Sammler erstellt.');
    }

    public function menuMinus($instructionId)
    {
        $instruction = instruction::find($instructionId);
        $hauptmenuspalteFilter = $instruction->hauptmenuspalte;

        $instructions = instruction::where('position', '>', 0)
            ->where('hauptmenuspalte', $hauptmenuspalteFilter)
            ->orderby('position')
            ->get();

        $instructionCount=($instructions->count()-1)*10+$hauptmenuspalteFilter;

        $instructionVerschiebens = instruction::where('hauptmenuspalte', '>' , $hauptmenuspalteFilter)
            ->orderby('hauptmenuspalte')
            ->orderby('position')
            ->get();

        foreach ($instructionVerschiebens as $instructionVerschieben) {
            instruction::find($instructionVerschieben->id)->update([
                'hauptmenuspalte' => $instructionVerschieben->hauptmenuspalte+$instructionCount,
                'bearbeiter_id'   => Auth::id(),
                'updated_at'      => Carbon::now()
            ]);
        }

        foreach ($instructions as $instruction) {
            $hauptmenuspalteFilter=$hauptmenuspalteFilter+10;
            instruction::find($instruction->id)->update([
                'hauptmenu'       => 1,
                'hauptmenuspalte' => $hauptmenuspalteFilter,
                'position'        => 10,
                'bearbeiter_id'   => Auth::id(),
                'updated_at'      => Carbon::now()
            ]);
        }

        instruction::find($instructionId)->update([
            'hauptmenu'     => 1,
            'position'      => 10,
            'bearbeiter_id' => Auth::id(),
            'updated_at'    => Carbon::now()
        ]);
        return Redirect()->back()->with('success', 'Der Posten wurde zur letzten Position verschoben.');
    }

    public function menuPlus($instructionId)
    {
        $instructions = instruction::orderby('hauptmenuspalte')
            ->orderby('position')
            ->get();

        $first=0;
        $firstPosition=0;
        $positionAktuel=10;
        $positionOld=10;
        $hauptmenuspalteNew=10;
        foreach ($instructions as $instruction) {
            if($first==0)
            {
                if($instruction->hauptmenu=="3") {
                    $positionAktuel=$positionAktuel+10;
                }
                if($instruction->hauptmenu=="2"){
                    $hauptmenuspalteNew=$instruction->hauptmenuspalte;
                    $positionAktuel=0;
                }
            }

            if($first==1){
                $hauptmenuspalteNew=$instruction->hauptmenuspalte-10;
                instruction::find($instruction->id)->update([
                    'hauptmenuspalte' => $hauptmenuspalteNew,
                    'bearbeiter_id'   => Auth::id(),
                    'updated_at'      => Carbon::now()
                ]);
            }

            if($instructionId==$instruction->id) {
                    $first = 1;
                    instruction::find($instruction->id)->update([
                        'hauptmenu'       => 3,
                        'hauptmenuspalte' => $hauptmenuspalteNew,
                        'position'        => $positionAktuel+10,
                        'bearbeiter_id'   => Auth::id(),
                        'updated_at'      => Carbon::now()
                    ]);
            }
        }
        return Redirect()->back()->with('success', 'Die Informationsseite zum Sammelmenu umgewandelt.');
    }

    public function menuDelete($instructionId)
    {
        $positionNew=0;
        $instructions=instruction::where('hauptmenu' , 0)
            ->orderby('position')
            ->get();
        foreach ($instructions as $instruction) {
            $positionNew = $instruction->position + 10;
        }

        $instruction = instruction::find($instructionId);
        $hauptmenuspalteFilter = $instruction->hauptmenuspalte;

        instruction::find($instructionId)->update([
            'hauptmenu'       => 0,
            'hauptmenuspalte' => 0,
            'position'        => $positionNew,
            'bearbeiter_id'   => Auth::id(),
            'updated_at'      => Carbon::now()
        ]);

        $instructions=instruction::where('hauptmenuspalte' , $hauptmenuspalteFilter)
            ->where('hauptmenu' , 3)
            ->orderby('position')
            ->get();
        $positionNew=10;
        foreach ($instructions as $instruction) {
            instruction::find($instruction->id)->update([
                'position'        => $positionNew,
                'bearbeiter_id'   => Auth::id(),
                'updated_at'      => Carbon::now()
            ]);
            $positionNew=$positionNew+10;
        }
        return Redirect()->back()->with('success', 'Die Informationsseite aus dem Menu entfernt.');
    }

    public function MenuDown($instructionId)
    {
        $positionNew=0;
        $instructions=instruction::where('hauptmenu' , 3)
            ->where('hauptmenuspalte' , 10)
            ->orderby('position')
            ->get();
        foreach ($instructions as $instruction) {
            $positionNew = $instruction->position + 10;
        }

        instruction::find($instructionId)->update([
            'hauptmenu'       => 3,
            'hauptmenuspalte' => 10,
            'position'        => $positionNew,
            'bearbeiter_id'   => Auth::id(),
            'updated_at'      => Carbon::now()
        ]);
        return Redirect()->back()->with('success', 'Der Informationsseite aktiviert.');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $instructions = instruction::orderby('hauptmenuspalte')
                                   ->orderby('position')
                                   ->get();
        $instructionMaxs = instruction::orderby('hauptmenuspalte')
                                   ->orderby('position')
            ->get();
        if($instructionMaxs->count()>0){
            $instructionMaxID     = $instructionMaxs->last()->hauptmenuspalte;
        }
        else{
            $instructionMaxID=0;
        }
        return view('admin.instruction.index')->with(
            [
                'instructions'      => $instructions,
                'instructionMaxID'  => $instructionMaxID,
            ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.instruction.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
                'ueberschrift'  => 'required|max:50',
            ]
        );

        $instruction = new instruction([
                'ueberschrift'             => $request->ueberschrift,
                'visible'                  => "1",
                'hauptmenu'                => "1",
                'bearbeiter_id'            => Auth::id(),
                'user_id'                  => Auth::id(),
                'updated_at'               => Carbon::now(),
                'created_at'               => Carbon::now()
            ]
        );
        $instruction->save();

        $instructions = instruction::orderby('ueberschrift')->get();

        return view('admin.instruction.index')->with(
            [
                'instructions'    => $instructions
            ]);
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
            'ueberschrift'    => $request->ueberschrift,
            'beschreibung'    => $request->beschreibung,
            'bearbeiter_id'   => Auth::id(),
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

    public function menulevel1($instructionId , $positionFilter)
    {
        $positionNew=10;
        $instructions=instruction::where('hauptmenu' , 0)
            ->orderby('position')
            ->get();
        foreach ($instructions as $instruction) {
            $positionNew = $instruction->position + 10;
        }

        instruction::find($instructionId)->update([
            'hauptmenu'       => 0,
            'hauptmenuspalte' => 0,
            'position'        => $positionNew,
            'bearbeiter_id'   => Auth::id(),
            'updated_at'      => Carbon::now()
        ]);

        $instructions = instruction::where('hauptmenuspalte', '>=', $positionFilter)
            ->orderby('hauptmenuspalte')
            ->orderby('position')
            ->get();

        $hauptmenuspalteNew=10;
        $hauptmenuspalteOld=20;
        foreach ($instructions as $instruction) {
            $hauptmenuspalte=$instruction->hauptmenuspalte;
            if($hauptmenuspalteOld<$hauptmenuspalte && $hauptmenuspalte>20){
                $hauptmenuspalteNew=$hauptmenuspalteNew+10;
                $hauptmenuspalteOld=$hauptmenuspalte;
            }
            instruction::find($instruction->id)->update([
                'hauptmenuspalte' => $hauptmenuspalteNew,
                'bearbeiter_id'   => Auth::id(),
                'updated_at'      => Carbon::now()
            ]);
             if($instruction->hauptmenuspalte==10) {
                instruction::find($instruction->id)->update([
                    'hauptmenu' => 1,
                    'position'  => 10
                ]);
              $hauptmenuspalteNew=$hauptmenuspalteNew+10;
            }
        }
    }

}
