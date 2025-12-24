<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Wallet;

class WalletPolicy
{
    /**
     * Create a new policy instance.
     */
    public function add(User $user)
    {
        return $user->hasPermissionTo('add to wallet','web');
    }
    public function withdraw(User $user)
    {
        return $user->hasPermissionTo('withdraw from wallet','web');
    }
}
