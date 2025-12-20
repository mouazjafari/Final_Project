<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchAddressRequest extends FormRequest
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
            'search_city_id'=>['sometimes','string','exists:cities,id'],
            'search_area'=>['sometimes','string'],
            'Longitude'=>['sometimes','decimal'],
            'Langitude'=>['sometimes','decimal'],
            'filter_by'=>['sometimes','string'],
            'pagination'=>['sometimes','string'],
            'sort_city_id'=>['sometimes','integer','exists:cities,id'],
            'sort_street'=>['sometimes','string'],
            'sort_area'=>['sometimes','string'],
        ];
    }
    public function messages()
    {
        return [
            'search_city_id.string'=>'City ID must be string',
            'search_city_id.exists'=>'City not found',
            'search_area.string'=>'Area must be string',
            'Longitude.decimal'=>'Longitude must be decimal',
            'Langitude.decimal'=>'Langitude must be decimal',
            'filter_by.string'=>'Filter by must be string',
            'pagination.string'=>'Pagination must be string',
            'sort_city_id.integer'=>'City ID must be integer',
            'sort_city_id.exists'=>'City not found',
            'sort_street.string'=>'Street must be string',
            'sort_area.string'=>'Area must be string',
        ];
    }
}
