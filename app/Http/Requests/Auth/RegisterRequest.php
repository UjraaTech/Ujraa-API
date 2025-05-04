<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'language' => ['sometimes', 'string', 'in:ar,en']
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'This email is already registered',
            'phone.unique' => 'This phone number is already registered',
            'password.confirmed' => 'Password confirmation does not match'
        ];
    }
}