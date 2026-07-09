<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('pages.public')]
class extends Component {

    public string $token = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function mount(string $token)
    {
        $this->token = $token;
        $this->email = request()->query('email', '');
    }

    public function resetPassword()
    {
        $credentials = $this->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', 'min:8', 'max:255'],
        ]);

        $status = Password::reset(
            [...$credentials, 'token' => $this->token],
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            session()->flash('success', 'Your password has been reset. You may now log in.');

            return redirect()->route('login');
        }

        $this->addError('email', __($status));
    }

};
?>

<div class="row">
    <h1 class="h3 mb-3 fw-normal text-center">Reset Password</h1>

    <div>
        <form wire:submit="resetPassword">

            <div class="mb-3">
                <label for="fid-email" class="form-label">Email Address</label>
                <input type="email" wire:model="email" id="fid-email" placeholder="name@example.com"
                       value="{{ $email }}"
                       class="form-control @error('email') is-invalid @enderror">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="fid-password" class="form-label">New Password</label>
                <input type="password" wire:model="password" id="fid-password"
                       class="form-control @error('password') is-invalid @enderror">
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="fid-password-confirm" class="form-label">Confirm New Password</label>
                <input type="password" wire:model="password_confirmation" id="fid-password-confirm"
                       class="form-control">
            </div>

            <div class="d-grid gap-2 col-12 mx-auto">
                <button type="submit" class="btn btn-primary mb-3">Reset Password</button>
            </div>
        </form>

        <p class="text-muted text-center">
            <a href="{{ route('login') }}">Login</a>
        </p>
    </div>
</div>
