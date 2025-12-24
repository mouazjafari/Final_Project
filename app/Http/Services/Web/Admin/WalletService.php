<?php

namespace App\Http\Services\Web\Admin;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WalletService
{
    /**
     * إضافة رصيد لمحفظة مستخدم
     */
    public function addBalance(int $userId, array $data)
    {
        $user = User::with('wallet')->findOrFail($userId);

        if (!$user->wallet) {
            $user->wallet()->create(['balance' => 0]);
        }

        $transaction = $user->wallet->deposit($data, Auth::id());

        return [
            'success' => true,
            'wallet' => $user->wallet,
            'transaction' => $transaction,
        ];
    }

    /**
     * سحب رصيد من محفظة مستخدم
     */
    public function withdrawBalance(int $userId, array $data)
    {
        return DB::transaction(function () use ($userId, $data) {
            $amount = $data['amount'];
            $notes = $data['notes'];
            $user = User::with('wallet')->findOrFail($userId);

            if (!$user->wallet) {
                throw new \Exception('المحفظة غير موجودة');
            }

            if ($user->wallet->balance < $amount) {
                throw new \Exception('الرصيد غير كافي للسحب');
            }

            $transaction = $user->wallet->withdraw($data, Auth::id());

            return [
                'success' => true,
                'wallet' => $user->wallet,
                'transaction' => $transaction,
            ];
        });
    }

    /**
     * الحصول على جميع المحافظ مع المستخدمين
     */
    public function getAllWallets()
    {
        return Wallet::with(['user', 'transactions' => function ($q) {
            $q->latest()->limit(5);
        }])->get();
    }

    /**
     * الحصول على محفظة مستخدم معين مع السجل الكامل
     */
    public function getUserWalletDetails(int $userId)
    {
        $user = User::with(['wallet.transactions.admin'])->findOrFail($userId);

        return [
            'user' => $user,
            'wallet' => $user->wallet,
            'transactions' => $user->wallet ? $user->wallet->transactions : [],
        ];
    }
}
