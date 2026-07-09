<?php

use App\Enum\Roles;
use App\Models\User;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('pages.public')]
class extends Component {

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public function mount()
    {
        abort_unless(config('app.registration_enabled'), 403);

        if (auth()->check()) {
            auth()->logout();
        }
    }

    public function register()
    {
        abort_unless(config('app.registration_enabled'), 403);

        $this->validate([
            'email' => ['required', 'email:rfc', Rule::unique('users', 'email')],
            'name' => ['required', 'min:3', Rule::unique('users', 'name')],
            'password' => ['required', 'min:8', 'max:255'],
        ]);

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => password_hash($this->password, PASSWORD_DEFAULT),
            'role' => Roles::Member,
        ]);

        auth()->login($user);

        session()->flash('success', 'Registration successful. You are now logged in!');

        return redirect()->route('dashboard');
    }

};
?>

<div class="row">
    <h1 class="h3 mb-3 fw-normal text-center">Account Registration</h1>

    <div>
        <form wire:submit="register">

            <div class="mb-3">
                <label for="fid-email" class="form-label">Email Address</label>
                <input type="email" wire:model="email" id="fid-email" placeholder="name@example.com"
                       class="form-control @error('email') is-invalid @enderror">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="fid-name" class="form-label">Name</label>
                <input type="text" wire:model="name" id="fid-name" placeholder="Your Name"
                       class="form-control @error('name') is-invalid @enderror">
                @error('name')
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

            <div class="d-grid gap-2 col-12 mx-auto">
                <button type="submit" class="btn btn-primary mb-3" data-test="btn-register">Register</button>
            </div>

        </form>

        <p class="text-muted text-center">
            <a href="{{ route('password.request') }}">Forgot Password?</a> |
            <a href="{{ route('login') }}">Login</a>
        </p>
    </div>
</div>
