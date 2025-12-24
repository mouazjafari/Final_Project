<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    protected $fillable = [
        'wallet_id',
        'admin_id',
        'type',
        'amount',
        'balance_before',
        'balance_after',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
    ];

    // العلاقة مع المحفظة
    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    // العلاقة مع الأدمن اللي عمل العملية
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
