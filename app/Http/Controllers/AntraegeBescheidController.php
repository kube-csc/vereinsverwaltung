<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Services\ApplicationNotifier;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class AntraegeBescheidController extends Controller
{
    public function __invoke(int $application)
    {
        $application = Application::findOrFail($application);

        $isOwner = $application->user_id === auth()->id() && Gate::allows('antraege.stellen');
        abort_unless($isOwner || Gate::allows('antraege.pruefen'), 403);
        abort_if(ApplicationNotifier::noticeType($application) === null, 404);

        // Auf das gespeicherte Bescheid-PDF zurückgreifen; keine Neuerzeugung
        if (empty($application->bescheid_pdf_path) || !Storage::disk('public')->exists($application->bescheid_pdf_path)) {
            abort(404, 'Der Bescheid wurde noch nicht erstellt oder ist nicht verfügbar.');
        }

        $pdfContent = Storage::disk('public')->get($application->bescheid_pdf_path);

        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="Bescheid-Antrag-' . $application->id . '.pdf"',
        ]);
    }
}
