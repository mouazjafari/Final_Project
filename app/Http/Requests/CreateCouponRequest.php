<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateCouponRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => 'required|string|max:50|unique:coupons,code',
            'type' => 'required|in:percentage,fixed',
            'amount' => 'required|numeric|min:0.01',
            'max_uses' => 'nullable|integer|min:1',
            'starts_at' => 'nullable|date|before_or_equal:expires_at',
            'expires_at' => 'nullable|date|after:now',
            'description' => 'nullable|string|max:500',
            'allowed_users' => 'nullable|array',
            'allowed_users.*' => 'integer|exists:users,id',
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'كود الكوبون مطلوب',
            'code.unique' => 'هذا الكود موجود مسبقاً',
            'type.required' => 'نوع الكوبون مطلوب',
            'type.in' => 'النوع يجب أن يكون نسبة أو رقم ثابت',
            'amount.required' => 'القيمة مطلوبة',
            'amount.min' => 'القيمة يجب أن تكون أكبر من 0',
            'max_uses.min' => 'الحد الأقصى يجب أن يكون 1 على الأقل',
            'starts_at.before_or_equal' => 'تاريخ البداية يجب أن يكون قبل تاريخ الانتهاء',
            'expires_at.after' => 'تاريخ الانتهاء يجب أن يكون في المستقبل',
            'allowed_users.*.exists' => 'أحد المستخدمين المحددين غير موجود',
        ];
    }

    /**
     * تحضير البيانات قبل Validation
     */
    protected function prepareForValidation()
    {
        // تحويل الكود لأحرف كبيرة
        if ($this->has('code')) {
            $this->merge([
                'code' => strtoupper($this->code),
            ]);
        }

        // إذا في allowed_users وجاي كـ JSON string
        if ($this->has('allowed_users') && is_string($this->allowed_users)) {
            $this->merge([
                'allowed_users' => json_decode($this->allowed_users, true),
            ]);
        }
    }
}
