<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UserRegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return config('app.registration_enabled');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        if ($this->isMethod('GET')) {
            return [];
        }

        return [
            'email' => ['required', 'email:rfc', 'unique:users'],
            'name' => ['required', 'min:3', 'unique:users'],
            'password' => 'required|min:8|max:255',
        ];
    }

    protected function passedValidation(): void
    {
        if ($this->has('password')) {
            $this->merge(
                ['password' => password_hash($this->input('password'), PASSWORD_DEFAULT)]
                // ['password' => bcrypt($this->input('password'))]
            );
        }
    }

    public function validated($key = null, $default = null): array
    {
        if ($this->has('password')) {
            return array_merge(parent::validated(), ['password' => $this->input('password')]);
        }

        return parent::validated();
    }
}
