<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\ApplicationDocument;
use App\Models\ApplicationType;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class AntraegeController extends Controller
{
    public function index()
    {
        Gate::authorize('antraege.stellen');

        return view('antraege.meine-antraege');
    }

    public function create(string $slug)
    {
        Gate::authorize('antraege.stellen');

        $type = ApplicationType::where('slug', $slug)
            ->where('aktiv', true)
            ->where(fn ($query) => $query->whereNull('frist')->orWhereDate('frist', '>=', today()))
            ->firstOrFail();

        return view('antraege.antrag-form', ['typeId' => $type->id]);
    }

    public function edit(int $application)
    {
        Gate::authorize('antraege.stellen');

        $draft = auth()->user()->applications()
            ->where('status', Application::STATUS_DRAFT)
            ->with('applicationType')
            ->findOrFail($application);

        return view('antraege.antrag-form', [
            'typeId' => $draft->application_type_id,
            'applicationId' => $draft->id,
        ]);
    }

    public function download(ApplicationDocument $applicationDocument)
    {
        $application = $applicationDocument->application;
        $isReviewer = Gate::allows('antraege.verwalten') || Gate::allows('antraege.entscheiden');
        $isOwner = $application->user_id === auth()->id() && Gate::allows('antraege.stellen');

        abort_unless($isOwner || $isReviewer, 403);

        return Storage::disk('local')->download(
            $applicationDocument->path,
            $applicationDocument->original_name,
            ['Content-Type' => $applicationDocument->mime_type]
        );
    }
}
