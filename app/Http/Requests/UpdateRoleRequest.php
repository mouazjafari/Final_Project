<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $roleId = $this->route('role');

        return [
            'name' => 'sometimes|string|max:100|unique:roles,name,' . $roleId,
            'permissions' => 'nullable|array',
            'permissions.*' => 'integer|exists:permissions,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'هذا الاسم موجود مسبقاً',
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
