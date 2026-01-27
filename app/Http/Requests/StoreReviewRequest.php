<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
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
            'order_id' => 'required|exists:orders,id',
            'comment' => 'nullable|string|required_without:rating',
            'rating' => 'nullable|integer|min:1|max:5|required_without:comment',
        ];
    }
    public function messages()
    {
        return [
            'order_id.exists' => 'The selected order does not exist.',
            'order_id.required' => 'order_id required.',
            'rating.required' => 'Please provide a rating between 1 and 5.',
            'rating.integer' => 'The rating must be an integer value.',
            'rating.min' => 'The rating must be at least 1.',
            'rating.max' => 'The rating may not be greater than 5.',
            'comment.string' => 'The comment must be a string.',
        ];
    }
}
