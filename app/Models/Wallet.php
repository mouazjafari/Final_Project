<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $fillable = ['user_id', 'balance'];

    protected $casts = [
        'balance' => 'decimal:2',
    ];

    // العلاقة مع المستخدم
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // العلاقة مع المعاملات
    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class)->latest();
    }

    // إضافة رصيد
    public function deposit(array $data ,?int $adminId = null)
    {
        $amount = $data['amount'];
        $notes = $data['notes'];

        $balanceBefore = $this->balance;
        $this->balance += $amount;
        $this->save();

        return $this->transactions()->create([
            'admin_id' => $adminId,
            'type' => 'deposit',
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $this->balance,
            'notes' => $notes,
        ]);
    }

    // سحب رصيد
    public function withdraw(array $data ,?int $adminId = null)
    {
        $amount = $data['amount'];
        $notes = $data['notes'];

        if ($this->balance < $amount) {
            throw new \Exception('الرصيد غير كافي');
        }

        $balanceBefore = $this->balance;
        $this->balance -= $amount;
        $this->save();

        return $this->transactions()->create([
            'admin_id' => $adminId,
            'type' => 'withdraw',
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $this->balance,
            'notes' => $notes,
        ]);
    }
}
