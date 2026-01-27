<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class updateReviewRequest extends FormRequest
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
            'comment' => 'nullable|string',
            'rating' => 'nullable|integer|min:1|max:5',
        ];
    }
    public function massage()
    {
        return [
            'rating.integer' => 'The rating must be an integer value.',
            'rating.min' => 'The rating must be at least 1.',
            'rating.max' => 'The rating may not be greater than 5.',
        ];
    }
}
