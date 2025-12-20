<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DesignFilterRequest extends FormRequest
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
            'name' => 'sometimes|string|max:255',
            'price' => 'sometimes|numeric|min:0',
            'user_id' => 'sometimes|exists:users,id',
            'sizes' => 'sometimes|array',
            'sizes.*' => 'integer|exists:sizes,id',
            'options' => 'sometimes|array',
            'options.*' => 'integer|exists:design_options,id',
        ];
    }
    public function messages(): array
    {
        return [
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name may not be greater than 255 characters.',
            'price.numeric' => 'The price must be a number.',
            'price.min' => 'The price must be at least 0.',
            'user_id.exists' => 'The selected user does not exist.',
            'sizes.array' => 'The sizes must be an array.',
            'sizes.*.integer' => 'Each size ID must be an integer.',
            'sizes.*.exists' => 'One or more selected sizes do not exist.',
            'options.array' => 'The options must be an array.',
            'options.*.integer' => 'Each option ID must be an integer.',
            'options.*.exists' => 'One or more selected options do not exist.',
        ];
    }
}
