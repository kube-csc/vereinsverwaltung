<?php

namespace App\Http\Livewire;

use App\Models\Invitation;
use App\Mail\UserInvitationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Livewire\Component;

class InvitationManager extends Component
{
    public $email;
    public $label;

    protected $rules = [
        'email' => 'nullable|email|unique:users,email|unique:invitations,email',
        'label' => 'nullable|string|max:255',
    ];

    public function sendInvitation()
    {
        $this->validate();

        if (empty($this->email) && empty($this->label)) {
            $this->addError('email', 'Entweder E-Mail oder Name/Label muss ausgefüllt werden.');
            return;
        }

        $invitation = Invitation::create([
            'email' => $this->email ?: null,
            'label' => $this->label ?: null,
            'token' => Str::random(64),
        ]);

        if ($this->email) {
            Mail::to($this->email)->send(new UserInvitationMail($invitation));
            $message = 'Einladung erfolgreich versendet an ' . $invitation->email;
        } else {
            $message = 'Einladungslink erfolgreich generiert für ' . $invitation->label;
        }

        $this->email = '';
        $this->label = '';
        session()->flash('message', $message);
    }

    public function deleteInvitation($id)
    {
        Invitation::findOrFail($id)->delete();
    }

    public function render()
    {
        return view('livewire.invitation-manager', [
            'invitations' => Invitation::latest()->get(),
        ]);
    }
}
