<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => ['required', 'string', 'min:4'],
            'remember' => ['nullable', 'boolean']
        ];
    }
    public function messages()
    {
        return [
            'email.required' => 'Email is required',
            'email.email' => 'Email must be a valid email address',
            'email.exists' => 'Email not found',
            'password.required' => 'Password is required',
            'password.string' => 'Password must be string',
            'password.min' => 'Password must be at least 4 characters',
            'remember.boolean' => 'Remember must be true or false',
        ];
    }
}
