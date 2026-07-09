<?php

namespace Tests\Feature\Auth;

use App\Enum\Roles;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_when_enabled(): void
    {
        config(['app.registration_enabled' => true]);

        $response = $this->post('/register', [
            'name' => 'Jane Doe',
            'email' => 'jane@gmail.com',
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('users', [
            'email' => 'jane@gmail.com',
            'role' => Roles::Member->value,
        ]);
    }

    public function test_registration_blocked_when_disabled(): void
    {
        config(['app.registration_enabled' => false]);

        $this->get('/register')->assertForbidden();

        $response = $this->post('/register', [
            'name' => 'Jane Doe',
            'email' => 'jane@gmail.com',
            'password' => 'password123',
        ]);

        $response->assertForbidden();
        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'jane@gmail.com']);
    }

    public function test_registered_user_defaults_to_member_role(): void
    {
        config(['app.registration_enabled' => true]);

        $this->post('/register', [
            'name' => 'Spoofed Admin',
            'email' => 'spoofed@gmail.com',
            'password' => 'password123',
            'role' => 'admin',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'spoofed@gmail.com',
            'role' => Roles::Member->value,
        ]);
    }
}
