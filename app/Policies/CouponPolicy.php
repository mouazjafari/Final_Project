<?php

namespace App\Policies;

use App\Models\Coupon;
use App\Models\User;

class CouponPolicy
{
    public function view(User $user): bool
    {
        return $user->hasPermissionTo('view coupons', 'web');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create coupons', 'web');
    }

    public function update(User $user): bool
    {
        return $user->hasPermissionTo('edit coupons', 'web');
    }

    public function delete(User $user): bool
    {
        return $user->hasPermissionTo('delete coupons', 'web');
    }
    public function apply(User $user): bool
    {
        return $user->hasPermissionTo('apply coupon', 'api');
    }
}
