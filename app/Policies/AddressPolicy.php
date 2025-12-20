<?php

namespace App\Policies;

use App\Http\Enum\RoleUserEnum;
use App\Models\Address;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AddressPolicy
{


    /**
     * Determine whether the user can view the model.
     */

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('add address');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Address $address): bool
    {
        return $user->hasPermissionTo('update address') && $user->id === $address->user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Address $address): bool
    {
        return $user->hasPermissionTo('delete address') && $user->id === $address->user->id;
    }
}
