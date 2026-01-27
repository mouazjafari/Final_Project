<?php

namespace App\Policies;

use App\Models\User;

class PermissionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view permissions', 'web');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create permissions', 'web');
    }

    public function delete(User $user): bool
    {
        return $user->hasPermissionTo('delete permissions', 'web');
    }
}
