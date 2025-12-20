<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAddressRequest extends FormRequest
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
            'address_id' => ['required', 'integer', 'exists:addresses,id'],
            'city_id' => ['sometimes', 'exists:cities,id'],
            'area' => ['sometimes', 'string'],
            'street' => ['sometimes', 'string'],
            'Longitude' => ['sometimes', 'decimal'],
            'Langitude' => ['sometimes', 'decimal'],
            'notes' => ['sometimes', 'string'],
        ];
    }
    public function messages()
    {
        return [
            'address_id.required' => 'Address ID is required',
            'address_id.integer' => 'Address ID must be integer',
            'address_id.exists' => 'Address not found',
            'city_id.exists' => 'City not found',
            'area.string' => 'Area must be string',
            'street.string' => 'Street must be string',
            'Longitude.decimal' => 'Longitude must be decimal',
            'Langitude.decimal' => 'Langitude must be decimal',
            'notes.string' => 'Notes must be string',
        ];
    }
}
