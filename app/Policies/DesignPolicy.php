<?php

namespace App\Policies;

use App\Models\Design;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DesignPolicy
{
    /**
     * Create a new policy instance.
     */
    public function view(User $user): bool
    {
        return $user->hasPermissionTo('view designs');
    }
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view my designs');
    }
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create design');
    }
    public function update(User $user, Design $design): bool
    {
        return $design->user_id === Auth::id() && $user->hasPermissionTo('update design');
    }
    public function delete(User $user, Design $design): bool
    {
        return $design->user_id === Auth::id() && $user->hasPermissionTo('delete design');
    }
}
