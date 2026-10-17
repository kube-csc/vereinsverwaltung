<?php

namespace App\Http\Livewire;

use App\Models\BankAccount;
use Livewire\Component;

class Bankkonten extends Component
{
    public ?int $editingId = null;
    public string $kontoinhaber = '';
    public string $bank_name = '';
    public string $iban = '';
    public string $bic = '';

    protected function rules(): array
    {
        return [
            'kontoinhaber' => ['required', 'string', 'max:255'],
            'bank_name' => ['nullable', 'string', 'max:255'],
            'iban' => ['required', 'string', 'max:42', function ($attribute, $value, $fail) {
                if (! BankAccount::isValidIban($value)) {
                    $fail('Die IBAN ist ungültig.');
                }
            }],
            'bic' => ['nullable', 'string', 'regex:/^[A-Za-z]{6}[A-Za-z0-9]{2}([A-Za-z0-9]{3})?$/'],
        ];
    }

    protected function messages(): array
    {
        return [
            'kontoinhaber.required' => 'Bitte den Kontoinhaber angeben.',
            'iban.required' => 'Bitte die IBAN angeben.',
            'bic.regex' => 'Die BIC ist ungültig.',
        ];
    }

    /** Konten werden ausschließlich über den angemeldeten Benutzer geladen. */
    private function accounts()
    {
        return auth()->user()->bankAccounts();
    }

    public function save(): void
    {
        $data = $this->validate();
        $data['iban'] = strtoupper(preg_replace('/\s+/', '', $data['iban']));
        $data['bic'] = $data['bic'] !== '' ? strtoupper($data['bic']) : null;
        $data['bank_name'] = $data['bank_name'] !== '' ? $data['bank_name'] : null;

        if ($this->editingId) {
            $this->accounts()->findOrFail($this->editingId)->update($data);
        } else {
            $this->accounts()->create($data);
        }

        $this->cancel();
        session()->flash('message', 'Bankkonto gespeichert.');
    }

    public function edit(int $id): void
    {
        $account = $this->accounts()->findOrFail($id);

        $this->resetValidation();
        $this->editingId = $account->id;
        $this->kontoinhaber = $account->kontoinhaber;
        $this->bank_name = (string) $account->bank_name;
        $this->iban = $account->formattedIban();
        $this->bic = (string) $account->bic;
    }

    public function delete(int $id): void
    {
        // Soft Delete: bereits eingereichte Anträge behalten ihren Bankdaten-Stand
        $this->accounts()->findOrFail($id)->delete();

        if ($this->editingId === $id) {
            $this->cancel();
        }
        session()->flash('message', 'Bankkonto entfernt.');
    }

    public function cancel(): void
    {
        $this->reset(['editingId', 'kontoinhaber', 'bank_name', 'iban', 'bic']);
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.bankkonten', [
            'accounts' => $this->accounts()->orderBy('id')->get(),
        ]);
    }
}
