<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateAddressRequest extends FormRequest
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
    public function rules()
    {
        return [
            'city_id' => ['required', 'exists:cities,id'],
            'area' => ['required', 'string'],
            'street' => ['required', 'string'],
            'Longitude' => ['sometimes', 'decimal'],
            'Langitude' => ['sometimes', 'decimal'],
            'notes' => ['sometimes', 'string'],
        ];
    }
    public function messages()
    {
        return [
            'city_id.required' => 'City is required',
            'city_id.exists' => 'City not found',
            'area.required' => 'Area is required',
            'area.string' => 'Area must be string',
            'street.required' => 'Street is required',
            'street.string' => 'Street must be string',
            'Longitude.decimal' => 'Longitude must be decimal',
            'Langitude.decimal' => 'Langitude must be decimal',
            'notes.string' => 'Notes must be string',
        ];
    }
}
