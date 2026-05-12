<?php

namespace App\Actions\Fortify;

use App\Models\Invitation;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Create a newly registered user.
     *
     * @param  array  $input
     * @return \App\Models\User
     */
    public function create(array $input)
    {
        Validator::make($input, [
            'vorname' => ['required', 'string', 'max:40'],
            'nachname' => ['required', 'string', 'max:40'],
            'geschlecht' => ['required', 'string', 'max:1'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            'sportSections_id' => ['required', 'integer', 'exists:sport_sections,id'],
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['required', 'accepted'] : '',
            'token' => ['required', 'string', 'exists:invitations,token'],
        ])->after(function ($validator) use ($input) {
            if (isset($input['token'])) {
                $invitation = Invitation::where('token', $input['token'])->first();
                if ($invitation && $invitation->email !== $input['email']) {
                    $validator->errors()->add('email', 'Diese E-Mail-Adresse stimmt nicht mit der Einladung überein.');
                }
                if ($invitation && $invitation->registered_at) {
                    $validator->errors()->add('token', 'Diese Einladung wurde bereits verwendet.');
                }
            }
        })->validate();

        return DB::transaction(function () use ($input) {
            return tap(User::create([
                'name' => $input['vorname'] . ' ' . $input['nachname'],
                'vorname' => $input['vorname'],
                'nachname' => $input['nachname'],
                'geschlecht' => $input['geschlecht'],
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
                'admin' => 1,
                'sportSections_id' => $input['sportSections_id'],
            ]), function (User $user) use ($input) {
                Invitation::where('token', $input['token'])->update([
                    'registered_at' => now(),
                    'user_id' => $user->id
                ]);
                $this->createTeam($user);
            });
        });
    }

    /**
     * Create a personal team for the user.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    protected function createTeam(User $user)
    {
        $user->ownedTeams()->save(Team::forceCreate([
            'user_id' => $user->id,
            'name' => explode(' ', $user->name, 2)[0]."'s Team",
            'personal_team' => true,
        ]));
    }
}
