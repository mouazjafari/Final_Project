<?php

namespace App\Http\Services\Web\Admin;

use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class RoleService
{
    public function getAllRoles()
    {
        return Role::withCount('permissions', 'users')->get();
    }

    public function createRole(array $data)
    {
        return DB::transaction(function () use ($data) {
            $role = Role::create([
                'name' => $data['name'],
                'guard_name' => $data['guard_name'],
            ]);

            if (!empty($data['permissions'])) {
                $role->syncPermissions($data['permissions']);
            }

            return $role;
        });
    }

    public function updateRole(Role $role, array $data)
    {
        return DB::transaction(function () use ($role, $data) {
            if (isset($data['name'])) {
                $role->update(['name' => $data['name']]);
            }

            if (isset($data['permissions'])) {
                $role->syncPermissions($data['permissions']);
            }

            return $role;
        });
    }

    public function deleteRole(Role $role)
    {
        // تحقق من عدم وجود مستخدمين
        if ($role->users()->count() > 0) {
            throw new \Exception('لا يمكن حذف دور مرتبط بمستخدمين');
        }

        return $role->delete();
    }
}
