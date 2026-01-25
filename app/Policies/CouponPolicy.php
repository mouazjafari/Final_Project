<?php

namespace App\Policies;

use App\Models\Coupon;
use App\Models\User;

class CouponPolicy
{
    /**
     * عرض الكوبونات
     */
    public function view(User $user): bool
    {
        return $user->hasPermissionTo('view coupons', 'web');
    }

    /**
     * إنشاء كوبون
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create coupons', 'web');
    }

    /**
     * تعديل كوبون
     */
    public function update(User $user): bool
    {
        return $user->hasPermissionTo('edit coupons', 'web');
    }

    /**
     * حذف كوبون
     */
    public function delete(User $user): bool
    {
        return $user->hasPermissionTo('delete coupons', 'web');
    }
}
