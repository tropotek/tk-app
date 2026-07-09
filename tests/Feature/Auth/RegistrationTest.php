<?php

namespace Tests\Feature\Auth;

use App\Enum\Roles;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_when_enabled(): void
    {
        config(['app.registration_enabled' => true]);

        Livewire::test('pages::register')
            ->set('name', 'Jane Doe')
            ->set('email', 'jane@gmail.com')
            ->set('password', 'password123')
            ->call('register')
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();

        $this->assertDatabaseHas('users', [
            'email' => 'jane@gmail.com',
            'role' => Roles::Member->value,
        ]);
    }

    public function test_registration_blocked_when_disabled(): void
    {
        config(['app.registration_enabled' => false]);

        Livewire::test('pages::register')->assertForbidden();

        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'jane@gmail.com']);
    }

    public function test_registered_user_defaults_to_member_role(): void
    {
        config(['app.registration_enabled' => true]);

        Livewire::test('pages::register')
            ->set('name', 'Spoofed Admin')
            ->set('email', 'spoofed@gmail.com')
            ->set('password', 'password123')
            ->call('register');

        $this->assertDatabaseHas('users', [
            'email' => 'spoofed@gmail.com',
            'role' => Roles::Member->value,
        ]);
    }
}
