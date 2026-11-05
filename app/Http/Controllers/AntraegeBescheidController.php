<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Services\ApplicationNotifier;
use Illuminate\Support\Facades\Gate;

class AntraegeBescheidController extends Controller
{
    public function __invoke(int $application)
    {
        $application = Application::findOrFail($application);

        $isOwner = $application->user_id === auth()->id() && Gate::allows('antraege.stellen');
        abort_unless($isOwner || Gate::allows('antraege.pruefen'), 403);
        abort_if(ApplicationNotifier::noticeType($application) === null, 404);

        return response(app(ApplicationNotifier::class)->pdf($application), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="Bescheid-Antrag-' . $application->id . '.pdf"',
        ]);
    }
}
