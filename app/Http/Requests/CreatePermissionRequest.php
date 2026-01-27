<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreatePermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100|unique:permissions,name',
            'guard_name' => 'required|in:api,web',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'اسم الصلاحية مطلوب',
            'name.unique' => 'هذا الاسم موجود مسبقاً',
            'guard_name.required' => 'Guard Name مطلوب',
            'guard_name.in' => 'Guard Name يجب أن يكون api أو web',
        ];
    }
}
