<?php

use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('pages.public')]
class extends Component {

    public string $email = '';

    public string $password = '';

    public bool $remember = false;

    public function mount()
    {
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }
    }

    public function login()
    {
        $this->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'min:8', 'max:255'],
        ]);

        $throttleKey = Str::lower($this->email).'|'.request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            throw ValidationException::withMessages([
                'email' => "Too many login attempts. Please try again in {$seconds} seconds.",
            ]);
        }

        if (! auth()->attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($throttleKey, 60);

            throw ValidationException::withMessages([
                'email' => 'These credentials do not match our records.',
            ]);
        }

        RateLimiter::clear($throttleKey);

        session()->regenerate();

        return redirect()->route('dashboard');
    }

};
?>

<div class="row">
    <h1 class="h3 mb-3 fw-normal text-center">Login</h1>

    <div>
        <form wire:submit="login">

            <div class="mb-3">
                <label for="fid-email" class="form-label">Email Address</label>
                <input type="email" wire:model="email" id="fid-email" placeholder="name@example.com"
                       class="form-control @error('email') is-invalid @enderror">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="fid-password" class="form-label">Password</label>
                <input type="password" wire:model="password" id="fid-password"
                       class="form-control @error('password') is-invalid @enderror">
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-check mb-3">
              <input class="form-check-input" type="checkbox" wire:model="remember" id="fid-rememberMe">
              <label class="form-check-label" for="fid-rememberMe">
                Remember Me
              </label>
            </div>

            <div class="d-grid gap-2 col-12 mx-auto">
                <button type="submit" class="btn btn-primary mb-3">Login</button>
            </div>

        </form>

        <p class="text-muted text-center">
            <a href="{{ route('password.request') }}">Forgot Password?</a>
            @if(config('app.registration_enabled'))
                | <a href="{{ route('register') }}">Register</a>
            @endif
        </p>
    </div>
</div>
