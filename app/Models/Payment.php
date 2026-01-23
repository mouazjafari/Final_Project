<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'order_id',
        'user_id',
        'payment_method',
        'stripe_payment_intent_id',
        'stripe_session_id', // ✅ جديد
        'stripe_charge_id',
        'amount',
        'status',
        'currency',
        'failure_reason',
        'metadata',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'metadata' => 'array',
    ];

    // العلاقة مع الطلب
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // العلاقة مع المستخدم
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // تحديد الدفعة كمكتملة
    public function markAsCompleted($stripeChargeId = null)
    {
        $this->update([
            'status' => 'completed',
            'stripe_charge_id' => $stripeChargeId,
            'paid_at' => now(),
        ]);
    }

    // تحديد الدفعة كفاشلة
    public function markAsFailed($reason = null)
    {
        $this->update([
            'status' => 'failed',
            'failure_reason' => $reason,
        ]);
    }
}
