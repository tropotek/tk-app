<?php

namespace App\Form;

use App\Models\User;
use Illuminate\Validation\Rule;
use Livewire\Form;

class ProfileForm extends Form
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
                'email:rfc',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->user),
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

        $this->name = $user->name;
        $this->email = $user->email;
    }

    public function save(): User
    {
        $this->validate();

        $this->user->update([
            'name' => $this->name,
            'email' => $this->email,
        ]);

        return $this->user;
    }
}
