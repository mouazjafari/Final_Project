<?php

namespace App\Policies;

use App\Models\Design_Option;
use App\Models\User;

class DesignOptionPolicy
{
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create design option', 'web');
    }
    public function update(User $user): bool
    {
        return $user->hasPermissionTo('update design option', 'web');
    }
    public function delete(User $user): bool
    {
        return $user->hasPermissionTo('delete design option', 'web');
    }
}
