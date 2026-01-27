<?php

namespace App\Policies;

use App\Http\Enum\OrderStatusEnum;
use App\Models\Order;
use App\Models\Review;
use App\Models\User;

class ReviewPolicy
{
    /**
     * التحقق من إمكانية إنشاء Review
     */
    public function create(User $user, Order $order): bool
    {
        // ✅ تحقق من:
        // 1. الـ Order يخص المستخدم
        // 2. الـ Order مكتمل
        // 3. المستخدم عنده صلاحية
        // 4. ما في review موجود مسبقاً

        $hasExistingReview = Review::where('order_id', $order->id)
            ->where('user_id', $user->id)
            ->exists();

        return $order->user_id === $user->id
            && $order->status === OrderStatusEnum::Completed->value
            && $user->hasPermissionTo('leave reviews', 'api')
            && !$hasExistingReview; // ✅ منع التكرار
    }

    /**
     * التحقق من إمكانية تعديل Review
     */
    public function update(User $user, Review $review): bool
    {
        return $review->user_id === $user->id
            && $user->hasPermissionTo('leave reviews', 'api');
    }

    /**
     * التحقق من إمكانية حذف Review
     */
    public function delete(User $user, Review $review): bool
    {
        return $review->user_id === $user->id
            && $user->hasPermissionTo('leave reviews', 'api');
    }
}
