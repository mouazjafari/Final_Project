<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100|unique:roles,name',
            'guard_name' => 'required|in:api,web',
            'permissions' => 'nullable|array',
            'permissions.*' => 'integer|exists:permissions,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'اسم الصلاحية مطلوب',
            'name.unique' => 'هذا الاسم موجود مسبقاً',
            'guard_name.required' => 'Guard Name مطلوب',
            'guard_name.in' => 'Guard Name يجب أن يكون api أو web',
            'permissions.array' => 'الصلاحيات يجب أن تكون مصفوفة',
            'permissions.*.exists' => 'إحدى الصلاحيات غير موجودة',
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->has('permissions') && is_string($this->permissions)) {
            $this->merge([
                'permissions' => json_decode($this->permissions, true),
            ]);
        }
    }
}
