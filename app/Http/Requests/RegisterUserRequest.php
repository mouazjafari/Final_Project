<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
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
            'name' => 'required|string|max:15',
            'email' => 'nullable|email|unique:users,email|required_without:phone_number',
            'phone_number' => 'nullable|string|unique:users,phone_number|required_without:email',
            'password' => 'required|string|min:4|max:20',
            'profile_image' => 'sometimes'
        ];
    }
    public function messages()
    {
        return [
            'name.required' => 'Name is required',
            'name.string' => 'Name must be string',
            'name.max' => 'Name must not exceed 15 characters',
            'email.required_without' => 'Email is required when phone number is not provided',
            'email.email' => 'Email must be a valid email address',
            'email.unique' => 'Email already exists',
            'phone_number.required_without' => 'Phone number is required when email is not provided',
            'phone_number.string' => 'Phone number must be string',
            'phone_number.unique' => 'Phone number already exists',
            'password.required' => 'Password is required',
            'password.string' => 'Password must be string',
            'password.min' => 'Password must be at least 4 characters',
            'password.max' => 'Password must not exceed 20 characters',
        ];
    }
}
