<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Seeder;

class WalletSeeder extends Seeder
{
    public function run(): void
    {
        // إنشاء محافظ لجميع المستخدمين الموجودين
        $users = User::whereDoesntHave('wallet')->get();

        foreach ($users as $user) {
            Wallet::create([
                'user_id' => $user->id,
                'balance' => 0.00,
            ]);
        }

    }
}
