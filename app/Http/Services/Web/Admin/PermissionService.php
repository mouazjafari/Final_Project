<?php

namespace App\Http\Services\Web\Admin;

use Spatie\Permission\Models\Permission;

class PermissionService
{
    public function getAllPermissions()
    {
        return Permission::withCount('roles')->get();
    }

    public function createPermission(array $data)
    {
        return Permission::create([
            'name' => $data['name'],
            'guard_name' => $data['guard_name'],
        ]);
    }

    public function deletePermission(Permission $permission)
    {
        return $permission->delete();
    }
}
