<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Jetstream\Http\Livewire\UpdateProfileInformationForm;
use Livewire\Livewire;
use Tests\TestCase;

class ProfileInformationTest extends TestCase
{
    use RefreshDatabase;

    public function test_current_profile_information_is_available()
    {
        $this->actingAs($user = User::factory()->create([
            'vorname' => 'Max',
            'nachname' => 'Mustermann',
            'telefon' => '01234 567890',
        ]));

        $component = Livewire::test(UpdateProfileInformationForm::class);

        $this->assertEquals($user->name, $component->state['name']);
        $this->assertEquals($user->email, $component->state['email']);
        $this->assertEquals($user->vorname, $component->state['vorname']);
        $this->assertEquals($user->nachname, $component->state['nachname']);
        $this->assertEquals($user->telefon, $component->state['telefon']);
    }

    public function test_profile_information_can_be_updated()
    {
        $this->actingAs($user = User::factory()->create());

        Livewire::test(UpdateProfileInformationForm::class)
                ->set('state', [
                    'name' => 'Test Name',
                    'email' => 'test@example.com',
                    'vorname' => 'Erika',
                    'nachname' => 'Musterfrau',
                    'telefon' => '09876 543210',
                ])
                ->call('updateProfileInformation');

        $this->assertEquals('Test Name', $user->fresh()->name);
        $this->assertEquals('test@example.com', $user->fresh()->email);
        $this->assertEquals('Erika', $user->fresh()->vorname);
        $this->assertEquals('Musterfrau', $user->fresh()->nachname);
        $this->assertEquals('09876 543210', $user->fresh()->telefon);
    }
}
