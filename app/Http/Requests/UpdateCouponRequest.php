<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCouponRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $couponId = $this->route('coupon');

        return [
            'code' => 'sometimes|string|max:50|unique:coupons,code,' . $couponId,
            'type' => 'sometimes|in:percentage,fixed',
            'amount' => 'sometimes|numeric|min:0.01',
            'max_uses' => 'nullable|integer|min:1',
            'starts_at' => 'nullable|date|before_or_equal:expires_at',
            'expires_at' => 'nullable|date|after:now',
            'is_active' => 'sometimes|boolean',
            'description' => 'nullable|string|max:500',
            'allowed_users' => 'nullable|array',
            'allowed_users.*' => 'integer|exists:users,id',
        ];
    }

    public function messages(): array
    {
        return [
            'code.unique' => 'هذا الكود موجود مسبقاً',
            'type.in' => 'النوع يجب أن يكون نسبة أو رقم ثابت',
            'amount.min' => 'القيمة يجب أن تكون أكبر من 0',
            'max_uses.min' => 'الحد الأقصى يجب أن يكون 1 على الأقل',
            'starts_at.before_or_equal' => 'تاريخ البداية يجب أن يكون قبل تاريخ الانتهاء',
            'expires_at.after' => 'تاريخ الانتهاء يجب أن يكون في المستقبل',
            'allowed_users.*.exists' => 'أحد المستخدمين المحددين غير موجود',
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->has('code')) {
            $this->merge([
                'code' => strtoupper($this->code),
            ]);
        }

        if ($this->has('allowed_users') && is_string($this->allowed_users)) {
            $this->merge([
                'allowed_users' => json_decode($this->allowed_users, true),
            ]);
        }
    }
}
