<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
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
            'address_id' => 'sometimes|exists:addresses,id',

            'design_id' => 'sometimes|exists:designs,id',

            'quantity' => 'sometimes|integer|min:1',

            'options' => 'sometimes|array',
            'options.*' => 'integer|exists:design_options,id',

            'size_id' => 'sometimes|exists:sizes,id',

            'notes' => 'nullable|string'
        ];
    }
    public function messages(): array
    {
        return [
            'address_id.required' => 'Address ID is required.',
            'address_id.exists' => 'The selected address does not exist.',

            'design_id.required' => 'Design ID is required.',
            'design_id.exists' => 'The selected design does not exist.',

            'quantity.required' => 'Quantity is required.',
            'quantity.integer' => 'Quantity must be an integer.',
            'quantity.min' => 'Quantity must be at least 1.',

            'options.array' => 'Options must be an array.',
            'options.*.integer' => 'Each option must be an integer.',
            'options.*.exists' => 'One or more selected options do not exist.',

            'size_id.required' => 'Size ID is required.',
            'size_id.exists' => 'The selected size does not exist.',

            'notes.string' => 'Notes must be a string.',
            'notes.max' => 'Notes must not exceed 255 characters.',

        ];
    }
}
