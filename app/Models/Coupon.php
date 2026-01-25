<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'type',
        'amount',
        'max_uses',
        'used_count',
        'starts_at',
        'expires_at',
        'is_active',
        'description',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    // ==================== العلاقات ====================

    public function usages()
    {
        return $this->hasMany(CouponUsage::class);
    }

    public function allowedUsers()
    {
        return $this->belongsToMany(User::class, 'coupon_user');
    }

    // ==================== دوال مساعدة ====================

    /**
     * فحص شامل: هل الكوبون صالح للاستخدام؟
     */
    public function isValid(): bool
    {
        // 1. هل الكوبون مفعل؟
        if (!$this->is_active) {
            return false;
        }

        // 2. هل بدأ تاريخ الصلاحية؟
        if ($this->starts_at && now()->lt($this->starts_at)) {
            return false;
        }

        // 3. هل انتهى تاريخ الصلاحية؟
        if ($this->expires_at && now()->gt($this->expires_at)) {
            return false;
        }

        // 4. هل وصل للحد الأقصى من الاستخدامات؟
        if ($this->max_uses && $this->used_count >= $this->max_uses) {
            return false;
        }

        return true;
    }

    /**
     * هل يمكن للمستخدم استخدام الكوبون؟
     */
    public function canBeUsedBy(int $userId): bool
    {
        // 1. هل الكوبون صالح أصلاً؟
        if (!$this->isValid()) {
            return false;
        }

        // 2. هل المستخدم استخدمه من قبل؟
        $alreadyUsed = $this->usages()->where('user_id', $userId)->exists();
        if ($alreadyUsed) {
            return false;
        }

        // 3. هل الكوبون محدد لمستخدمين معينين؟
        $hasAllowedUsers = $this->allowedUsers()->count() > 0;
        if ($hasAllowedUsers) {
            $isAllowed = $this->allowedUsers()->where('users.id', $userId)->exists();
            if (!$isAllowed) {
                return false;
            }
        }

        return true;
    }

    /**
     * حساب الخصم بناءً على سعر الأوردر
     */
    public function calculateDiscount(float $orderTotal): float
    {
        if ($this->type === 'percentage') {
            // نسبة مئوية
            return ($orderTotal * $this->amount) / 100;
        } else {
            // رقم ثابت
            // تأكد أن الخصم لا يتجاوز سعر الأوردر
            return min($this->amount, $orderTotal);
        }
    }

    /**
     * تسجيل استخدام الكوبون
     */
    public function recordUsage(int $userId, int $orderId, float $discountAmount)
    {
        return DB::transaction(function () use ($userId, $orderId, $discountAmount) {
            // إنشاء سجل الاستخدام
            $usage = $this->usages()->create([
                'user_id' => $userId,
                'order_id' => $orderId,
                'discount_amount' => $discountAmount,
            ]);

            // زيادة عداد الاستخدام
            $this->increment('used_count');

            return $usage;
        });
    }

    /**
     * التحقق من أن سعر الأوردر كافي (للكوبونات الثابتة)
     */
    public function isOrderTotalSufficient(float $orderTotal): bool
    {
        if ($this->type === 'fixed') {
            return $orderTotal >= $this->amount;
        }
        return true; // النسب المئوية دائماً OK
    }
}
