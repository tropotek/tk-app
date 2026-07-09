<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('pages.public')]
class extends Component {

    public string $email = '';

    public function sendResetLink()
    {
        $this->validate(['email' => ['required', 'email']]);

        Password::sendResetLink(['email' => $this->email]);

        session()->flash('success', 'If that email address is registered, a password reset link has been sent.');

        $this->reset('email');
    }

};
?>

<div class="row">
    <h1 class="h3 mb-3 fw-normal text-center">Forgot Password</h1>

    <div>
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form wire:submit="sendResetLink">

            <div class="mb-3">
                <label for="fid-email" class="form-label">Email Address</label>
                <input type="email" wire:model="email" id="fid-email" placeholder="name@example.com"
                       class="form-control @error('email') is-invalid @enderror">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-grid gap-2 col-12 mx-auto">
                <button type="submit" class="btn btn-primary mb-3">Send Reset Link</button>
            </div>
        </form>

        <p class="text-muted text-center">
            <a href="{{ route('login') }}">Login</a>
        </p>
    </div>
</div>
