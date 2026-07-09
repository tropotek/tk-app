<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Livewire\Livewire;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_request_password_reset_link(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        Livewire::test('pages::forgot-password')
            ->set('email', $user->email)
            ->call('sendResetLink');

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_user_can_reset_password_with_valid_token(): void
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        Livewire::test('pages::reset-password', ['token' => $token])
            ->set('email', $user->email)
            ->set('password', 'new-password123')
            ->set('password_confirmation', 'new-password123')
            ->call('resetPassword')
            ->assertRedirect(route('login'));

        Livewire::test('pages::login')
            ->set('email', $user->email)
            ->set('password', 'new-password123')
            ->call('login');

        $this->assertAuthenticatedAs($user);
    }

    public function test_reset_fails_with_invalid_token(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);

        Livewire::test('pages::reset-password', ['token' => 'invalid-token'])
            ->set('email', $user->email)
            ->set('password', 'new-password123')
            ->set('password_confirmation', 'new-password123')
            ->call('resetPassword')
            ->assertHasErrors('email');

        Livewire::test('pages::login')
            ->set('email', $user->email)
            ->set('password', 'password')
            ->call('login');

        $this->assertAuthenticatedAs($user);
    }
}
