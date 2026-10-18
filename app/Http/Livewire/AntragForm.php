<?php

namespace App\Http\Livewire;

use App\Models\Application;
use App\Models\ApplicationDocument;
use App\Models\ApplicationField;
use App\Models\ApplicationType;
use App\Models\BankAccount;
use App\Models\Club;
use App\Models\FundingPot;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Throwable;

class AntragForm extends Component
{
    use WithFileUploads;

    public ?int $typeId = null;
    public ?int $applicationId = null;
    public ?string $clubId = null;
    public ?string $bankAccountId = null;
    public ?string $fundingPotId = null;
    public array $formData = [];
    public array $uploads = [];

    public function mount(?int $typeId = null, ?int $applicationId = null): void
    {
        Gate::authorize('antraege.stellen');
        $this->typeId = $typeId;
        $this->applicationId = $applicationId;

        if ($applicationId) {
            $application = $this->draftApplication();
            $this->typeId = $application->application_type_id;
            $this->clubId = $application->club_id ? (string) $application->club_id : null;
            $this->bankAccountId = $application->bankAccount()->exists()
                ? (string) $application->bank_account_id
                : null;
            $this->fundingPotId = $application->funding_pot_id ? (string) $application->funding_pot_id : null;
            $this->formData = $application->answers ?? [];
        } else {
            $this->applicationType();
        }
    }

    private function authorizeApplicant(): void
    {
        Gate::authorize('antraege.stellen');
    }

    private function draftApplication(): Application
    {
        $this->authorizeApplicant();

        if (! $this->applicationId) {
            abort(404);
        }

        return auth()->user()->applications()
            ->where('status', Application::STATUS_DRAFT)
            ->with(['applicationType.fields' => fn ($query) => $query->where('aktiv', true), 'documents'])
            ->findOrFail($this->applicationId);
    }

    private function applicationType(): ApplicationType
    {
        $this->authorizeApplicant();

        if ($this->applicationId) {
            return $this->draftApplication()->applicationType;
        }

        if (! $this->typeId) {
            abort(404);
        }

        return ApplicationType::where('aktiv', true)
            ->where(fn ($query) => $query->whereNull('frist')->orWhereDate('frist', '>=', today()))
            ->with(['fields' => fn ($query) => $query->where('aktiv', true)])
            ->findOrFail($this->typeId);
    }

    private function validationRules(ApplicationType $type, bool $submitting): array
    {
        $rules = [
            'clubId' => [
                $submitting && $type->requires_club ? 'required' : 'nullable',
                'integer',
                Rule::exists('clubs', 'id')->where(fn ($query) => $query
                    ->where('user_id', auth()->id())
                    ->whereNull('deleted_at')),
            ],
            'bankAccountId' => [
                'nullable',
                'integer',
                Rule::exists('bank_accounts', 'id')->where(fn ($query) => $query
                    ->where('user_id', auth()->id())
                    ->whereNull('deleted_at')),
            ],
            'fundingPotId' => [
                $submitting && $type->requires_funding_pot ? 'required' : 'nullable',
                'integer',
                Rule::exists('application_type_funding_pot', 'funding_pot_id')
                    ->where('application_type_id', $type->id),
            ],
            'formData' => ['array'],
            'uploads' => ['array'],
        ];

        foreach ($type->fields as $field) {
            $key = $field->key;
            $required = $submitting && $field->required;

            if ($field->type === 'file') {
                $rules["uploads.{$key}"] = [
                    'nullable',
                    'file',
                    'mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx',
                    'max:10240',
                ];

                continue;
            }

            $fieldRules = [$required ? 'required' : 'nullable'];
            if ($field->type === 'checkbox' && $required) {
                $fieldRules = ['accepted'];
            } else {
                $fieldRules = array_merge($fieldRules, match ($field->type) {
                    'number' => ['numeric', 'min:0'],
                    'date' => ['date'],
                    'email' => ['email', 'max:255'],
                    'textarea' => ['string', 'max:10000'],
                    'select' => ['string', Rule::in($field->options ?? [])],
                    'checkbox' => ['boolean'],
                    default => ['string', 'max:5000'],
                });
            }

            $rules["formData.{$key}"] = $fieldRules;
        }

        return $rules;
    }

    public function saveDraft(): void
    {
        $this->persist(false);
    }

    public function submitApplication(): void
    {
        $this->persist(true);
    }

    private function persist(bool $submitting): void
    {
        $this->authorizeApplicant();
        $type = $this->applicationType();

        if ($submitting && $type->frist && $type->frist->isBefore(today())) {
            $this->addError('submission', 'Die Abgabefrist dieser Antragsart ist abgelaufen.');
            return;
        }

        $this->validate($this->validationRules($type, $submitting), [
            'clubId.required' => 'Bitte wählen Sie den antragstellenden Verein aus.',
            'clubId.exists' => 'Der ausgewählte Verein ist nicht verfügbar.',
            'bankAccountId.exists' => 'Das ausgewählte Bankkonto ist nicht mehr verfügbar.',
            'fundingPotId.required' => 'Bitte wählen Sie einen zugeordneten Fördertopf aus.',
            'fundingPotId.exists' => 'Der ausgewählte Fördertopf ist dieser Antragsart nicht zugeordnet.',
            'uploads.*.mimes' => 'Erlaubte Dateitypen: PDF, JPG, PNG, DOC, DOCX, XLS und XLSX.',
            'uploads.*.max' => 'Die Anlage darf höchstens 10 MB groß sein.',
        ]);

        if ($submitting) {
            foreach ($type->fields as $field) {
                if ($field->type === 'file' && $field->required
                    && empty($this->uploads[$field->key])
                    && ! $this->existingDocument($field->key)) {
                    $this->addError("uploads.{$field->key}", 'Bitte laden Sie die erforderliche Anlage hoch.');
                }
            }

            if ($this->getErrorBag()->count() > 0) {
                return;
            }
        }

        $application = $this->applicationId
            ? $this->draftApplication()
            : new Application([
                'application_type_id' => $type->id,
                'user_id' => auth()->id(),
                'status' => Application::STATUS_DRAFT,
                'answers' => [],
            ]);
        $currentAnswers = [];
        foreach ($type->fields as $field) {
            if ($field->type !== 'file' && array_key_exists($field->key, $this->formData)) {
                $currentAnswers[$field->key] = $this->formData[$field->key];
            }
        }
        $answers = array_merge($application->answers ?? [], $currentAnswers);
        $clubId = $type->requires_club && $this->clubId ? (int) $this->clubId : null;
        $accountId = $this->bankAccountId ? (int) $this->bankAccountId : null;
        $fundingPotId = $this->fundingPotId ? (int) $this->fundingPotId : null;
        $newPaths = [];
        $replacedPaths = [];

        try {
            $saved = DB::transaction(function () use ($application, $type, $answers, $clubId, $accountId, $fundingPotId, $submitting, &$newPaths, &$replacedPaths) {
                $club = $clubId
                    ? Club::where('user_id', auth()->id())->whereNull('deleted_at')->lockForUpdate()->findOrFail($clubId)
                    : null;
                $account = $accountId
                    ? auth()->user()->bankAccounts()->lockForUpdate()->findOrFail($accountId)
                    : null;
                $fundingPot = $fundingPotId
                    ? FundingPot::whereHas('applicationTypes', fn ($query) => $query->whereKey($type->id))
                        ->lockForUpdate()
                        ->findOrFail($fundingPotId)
                    : null;

                if ($submitting && (
                    ($type->requires_funding_pot && ! $fundingPot)
                    || ($fundingPot && ! $fundingPot->istOffen())
                )) {
                    return false;
                }

                $application->fill([
                    'application_type_id' => $type->id,
                    'funding_pot_id' => $fundingPot?->id,
                    'user_id' => auth()->id(),
                    'club_id' => $club?->id,
                    'bank_account_id' => $account?->id,
                    'bank_account_snapshot' => $submitting && $account ? [
                        'kontoinhaber' => $account->kontoinhaber,
                        'bank_name' => $account->bank_name,
                        'iban' => $account->iban,
                        'bic' => $account->bic,
                    ] : ($submitting ? null : $application->bank_account_snapshot),
                    'form_snapshot' => $submitting ? $type->fields->map(fn (ApplicationField $field) => [
                        'key' => $field->key,
                        'label' => $field->label,
                        'type' => $field->type,
                        'required' => $field->required,
                        'options' => $field->options,
                        'help_text' => $field->help_text,
                    ])->values()->all() : $application->form_snapshot,
                    'answers' => $answers,
                    'status' => $submitting ? Application::STATUS_SUBMITTED : Application::STATUS_DRAFT,
                    'submitted_at' => $submitting ? now() : null,
                ])->save();

                foreach ($type->fields as $field) {
                    $upload = $this->uploads[$field->key] ?? null;
                    if (! $upload) {
                        continue;
                    }

                    $filename = Str::uuid() . '.' . strtolower($upload->getClientOriginalExtension());
                    $path = $upload->storeAs('application-documents/' . auth()->id(), $filename, 'local');
                    if (! $path) {
                        throw new \RuntimeException('Die Anlage konnte nicht sicher gespeichert werden.');
                    }
                    $newPaths[] = $path;

                    $document = $application->documents()->where('field_key', $field->key)->first();
                    if ($document) {
                        $replacedPaths[] = $document->path;
                        $document->update([
                            'original_name' => $upload->getClientOriginalName(),
                            'path' => $path,
                            'mime_type' => $upload->getMimeType() ?: 'application/octet-stream',
                            'size' => $upload->getSize(),
                        ]);
                    } else {
                        $application->documents()->create([
                            'field_key' => $field->key,
                            'original_name' => $upload->getClientOriginalName(),
                            'path' => $path,
                            'mime_type' => $upload->getMimeType() ?: 'application/octet-stream',
                            'size' => $upload->getSize(),
                        ]);
                    }
                }

                return true;
            });
        } catch (Throwable $exception) {
            Storage::disk('local')->delete($newPaths);
            throw $exception;
        }

        if (! $saved) {
            Storage::disk('local')->delete($newPaths);
            $this->addError('fundingPotId', 'Der ausgewählte Fördertopf ist inzwischen geschlossen oder nicht mehr verfügbar. Bitte wählen Sie einen offenen Topf.');
            return;
        }

        Storage::disk('local')->delete($replacedPaths);
        session()->flash('message', $submitting ? 'Ihr Antrag wurde eingereicht.' : 'Der Entwurf wurde gespeichert.');
        $this->redirectRoute('antraege.meine');
    }

    private function existingDocument(string $fieldKey): ?ApplicationDocument
    {
        if (! $this->applicationId) {
            return null;
        }

        return $this->draftApplication()->documents->firstWhere('field_key', $fieldKey);
    }

    public function removeDocument(int $documentId): void
    {
        $application = $this->draftApplication();
        $document = $application->documents()->findOrFail($documentId);
        Storage::disk('local')->delete($document->path);
        $document->delete();
    }

    public function render()
    {
        $type = $this->applicationType();
        $application = $this->applicationId ? $this->draftApplication() : null;
        $mappedFundingPots = $type->fundingPots()->with('program')->get();
        $fundingPots = $mappedFundingPots->filter(fn (FundingPot $pot) => $pot->istOffen())->values();
        if ($this->fundingPotId) {
            $selectedPot = $mappedFundingPots->firstWhere('id', (int) $this->fundingPotId);
            if ($selectedPot && ! $fundingPots->contains('id', $selectedPot->id)) {
                $fundingPots->prepend($selectedPot);
            }
        }

        return view('livewire.antrag-form', [
            'applicationType' => $type,
            'accounts' => auth()->user()->bankAccounts()->orderBy('kontoinhaber')->get(),
            'clubs' => $type->requires_club
                ? Club::where('user_id', auth()->id())->orderBy('clubname')->get()
                : collect(),
            'fundingPots' => $fundingPots,
            'documents' => $application?->documents ?? collect(),
            'deadlinePassed' => $type->frist?->isBefore(today()) ?? false,
        ]);
    }
}
