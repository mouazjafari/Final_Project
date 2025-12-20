<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateUserRequest extends FormRequest
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
    // في UpdateUserRequest
    public function rules(): array
    {
        $userId = Auth::id();

        return [
            'name' => 'sometimes|string|max:20',
            'email' => 'sometimes|nullable|email|unique:users,email,' . $userId,
            'phone_number' => 'sometimes|nullable|string|max:20',
            'password' => 'sometimes|string|min:4|max:20|confirmed',
            'profile_image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048'
        ];
    }
    public function messages()
    {
        return [
            'name.string' => 'Name must be string',
            'name.max' => 'Name must not exceed 20 characters',
            'email.email' => 'Email must be a valid email address',
            'email.unique' => 'Email already exists',
            'phone_number.string' => 'Phone number must be string',
            'phone_number.max' => 'Phone number must not exceed 20 characters',
            'password.string' => 'Password must be string',
            'password.min' => 'Password must be at least 4 characters',
            'password.max' => 'Password must not exceed 20 characters',
            'password.confirmed' => 'Password confirmation does not match',
            'profile_image.image' => 'Profile image must be an image file',
            'profile_image.mimes' => 'Profile image must be a file of type: jpeg, png, jpg, gif',
            'profile_image.max' => 'Profile image must not exceed 2048 kilobytes',
        ];
    }
}
