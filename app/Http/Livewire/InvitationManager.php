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

    protected $rules = [
        'email' => 'required|email|unique:users,email|unique:invitations,email',
    ];

    public function sendInvitation()
    {
        $this->validate();

        $invitation = Invitation::create([
            'email' => $this->email,
            'token' => Str::random(64),
        ]);

        Mail::to($this->email)->send(new UserInvitationMail($invitation));

        $this->email = '';
        session()->flash('message', 'Einladung erfolgreich versendet an ' . $invitation->email);
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
