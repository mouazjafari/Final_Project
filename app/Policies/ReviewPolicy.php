<?php

namespace App\Policies;

use App\Http\Enum\OrderStatusEnum;
use App\Models\Order;
use App\Models\Review;
use App\Models\User;

class ReviewPolicy
{
    /**
     * Create a new policy instance.
     */
    public function create(User $user, Order $order): bool
    {
        return $order->user_id === $user->id && $order->status === OrderStatusEnum::Completed->value && $user->hasPermissionTo('leave reviews', 'api');
    }
    public function update(User $user, Review $review): bool
    {
        return $review->user_id === $user->id && $user->hasPermissionTo('leave reviews', 'api');
    }
    public function delete(User $user, Review $review): bool
    {
        return $review->user_id === $user->id && $user->hasPermissionTo('leave reviews', 'api');
    }
}
