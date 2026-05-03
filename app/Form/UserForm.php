<?php

namespace App\Form;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Form;

class UserForm extends Form
{
    public ?User $user = null;

    public string $name = '';

    public string $email = '';

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => [
                'required',
                'email:rfc,dns',
                'max:255',
                Rule::unique('user','email')->ignore($this->user)
            ],
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'name' => 'first and middle names',
            'email' => 'email address',
        ];
    }

    public function load(User $user): void
    {
        $this->user = $user;

        $this->name = $user->name ?? '';
        $this->email = $user->email ?? '';
    }

    public function create(string $username): User
    {
        $this->validate();

        return User::create([
            'username' => $username,
            'name' => $username,
            'email' => $username,
            'password' => Hash::make(Str::random(40)),
        ]);
    }

    public function update(): void
    {
        $this->validate();

        $this->user->update($this->toArray());
    }
}
