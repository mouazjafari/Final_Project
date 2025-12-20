<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    /**
     * Create a new policy instance.
     */
    public function view(User $user)
    {
        return $user->hasPermissionTo('view order');
    }
    public function viewAny(User $user, Order $order)
    {
        return $order->user_id === $user->id && $user->hasPermissionTo('view order');
    }
    public function cancel(User $user, Order $order)
    {
        return $order->user_id === $user->id && $user->hasPermissionTo('cancel order');
    }
    public function update(User $user, Order $order)
    {
        return $order->user_id === $user->id && $user->hasPermissionTo('update order');
    }
    public function create(User $user)
    {
        return $user->hasPermissionTo('create order');
    }
}
