<?php

namespace App\Form;

use App\Enum\Roles;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Form;

class UserForm extends Form
{
    public ?User $user = null;

    public string $name = '';

    public string $email = '';

    public ?Roles $role = null;

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => [
                'required',
                'email:rfc',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->user),
            ],
            'role' => ['required', Rule::enum(Roles::class)],
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'name' => 'first and middle names',
            'email' => 'email address',
        ];
    }

    public function load(?User $user): void
    {
        $this->user = $user;

        $this->name = $user->name ?? '';
        $this->email = $user->email ?? '';
        $this->role = $user->role ?? Roles::Member;
    }

    public function save(): User
    {
        $this->validate();

        Gate::authorize($this->user ? 'update' : 'create', $this->user ?? User::class);
        Gate::authorize('assignRole', [User::class, $this->role]);

        if ($this->user) {
            $this->user->update([
                'name' => $this->name,
                'email' => $this->email,
                'role' => $this->role,
            ]);

            return $this->user;
        }

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'password' => Hash::make(Str::random(40)),
        ]);

        Password::sendResetLink(['email' => $user->email]);

        return $user;
    }
}
