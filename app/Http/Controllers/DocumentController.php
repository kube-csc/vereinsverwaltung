<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Instruction;
use App\Models\SportSection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function aktiv($documentId)
    {
        Document::find($documentId)->update([
            'visible'          => '1',
            'bearbeiter_id'    => Auth::user()->id,
            'updated_at'       => Carbon::now()
        ]);
        return Redirect()->back()->with('success' , 'Das Dokuemnt wurde sichtbar geschaltet.');
    }

    public function inaktiv($documentId)
    {
        Document::find($documentId)->update([
            'visible'          => '0',
            'footerStatus'     => 1,
            'bearbeiter_id'    => Auth::user()->id,
            'updated_at'       => Carbon::now()
        ]);
        return Redirect()->back()->with('success' , 'Das Dokuemnt wurde unsichtbar geschaltet.');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $documents = Document::orderby('dokumentenName')->get();

        return view('admin.document.index')->with(
            [
                'documents'    => $documents
            ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $instructions = instruction::orderby('ueberschrift')->get();

        $sportSections = SportSection::where('status' , '>' ,'0')->orderby('status')
            ->orderby('sportSection_id')
            ->orderby('abteilung')
            ->get();

        return view('admin.document.create')->with(
            [
                'instructions'  => $instructions,
                'sportSections' => $sportSections
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
        $request->validate(
            [
                'documentName'  => 'required|max:45',
            ]
        );

        $document= new Document(
            [
                'dokumentenName'   => $request->documentName,
                'instruction_id'   => $request->instruction_id,
                'sportSection_id'  => $request->sportSection_id,
                'visible'          => 1,
                'footerStatus'     => $request->footerStatus,
                'bearbeiter_id'    => Auth::user()->id,
                'user_id'          => Auth::user()->id,
                'updated_at'       => Carbon::now(),
                'created_at'       => Carbon::now()
            ]
        );
        $document->save();

        return redirect('/Dokumente/alle')->with(
            [
                'success' => 'Das Dokument <b>' . $request->postenMaenlich .'</b> wurden angelegt.'
            ]
        );

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function show(Document $document)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function edit($documentId)
    {
        $document = Document::find($documentId);

        $instructions = instruction::orderby('ueberschrift')->get();

        $sportSections = SportSection::where('status' , '>' ,'0')->orderby('status')
            ->orderby('sportSection_id')
            ->orderby('abteilung')
            ->get();

        return view('admin.document.edit',compact('document' ))->with(
            [
                'document'      => $document,
                'instructions'  => $instructions,
                'sportSections' => $sportSections,
            ]
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $documentId)
    {
        $request->validate(
            [
                'dokumentenName'  => 'required|max:45',
            ]
        );

        Document::find($documentId)->update([
            'dokumentenName'    => $request->dokumentenName,
            'instruction_id'    => $request->instruction_id,
            'sportSection_id'   => $request->sportSection_id,
            'footerStatus'      => $request->footerStatus,
            'visible'           => '1',
            'startDatum'        => Carbon::now(),
            'endDatum'          => '2099-12-31',
            'updated_at'        => Carbon::now()
        ]);

        if($request->documentFile){
            $extension = $request->documentFile->extension();
            $newDocumentName = 'dokument' . $documentId . '_' . str::random(4) . '.' . $extension;
            Storage::disk('public')->putFileAs(
                'dokumente/',
                $request->documentFile,
                $newDocumentName
            );

            $oldDocumentFile = Document::find($documentId);
            if(isset($oldDocumentFile->dokumentenFile)){
                Storage::disk('public')->delete('dokumente/'.$oldDocumentFile->dokumentenFile);
            }

            Document::find($documentId)->update([
                'dokumentenFile' => $newDocumentName,
            ]);
        }

        return redirect('/Dokumente/alle')->with(
            [
                 'success'  => 'Die Daten von den Dokument <b>' . $request->documentenName .'</b> wurden gespeichert.'
            ]
        );
    }

    public function documentDelete($documentId)
    {
        // ToDo: Der Dokumentenname der neu im Feld eingeben wurde geht verlohren
        $deleteDocumentFile = Document::find($documentId);
        if(isset($deleteDocumentFile->dokumentenFile)){
            Storage::disk('public')->delete('dokumente/'.$deleteDocumentFile->dokumentenFile);
        }
        Document::find($documentId)->update(
            [
              'dokumentenFile' => Null,
            ]);

        $document = Document::find($documentId);
        return redirect('/Dokumente/edit/'.$documentId)->with(
            [
                'success'  => 'Die Datei vom Dokument <b>' . $document->documentenName . '</b> wurden gelöscht.'
            ]
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function destroy(Document $document)
    {
        //
    }
}
