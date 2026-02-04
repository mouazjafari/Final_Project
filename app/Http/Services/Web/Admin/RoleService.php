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
            // دائماً إنشاء الـ role بـ guard = web
            $role = Role::create([
                'name' => $data['name'],
                'guard_name' => 'web', // دائماً web
            ]);

            if (!empty($data['permissions'])) {
                // فلتر الـ Permissions - خلي بس اللي عندهم web guard
                $validPermissions = \Spatie\Permission\Models\Permission::whereIn('id', $data['permissions'])
                    ->where('guard_name', 'web') // دائماً web
                    ->pluck('id')
                    ->toArray();

                if (!empty($validPermissions)) {
                    $role->syncPermissions($validPermissions);
                }
            }

            // إضافة access dashboard تلقائياً لكل الأدوار
            $dashboardPermission = \Spatie\Permission\Models\Permission::where('name', 'access dashboard')
                ->where('guard_name', 'web')
                ->first();
            if ($dashboardPermission && !$role->hasPermissionTo($dashboardPermission)) {
                $role->givePermissionTo($dashboardPermission);
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
                // ✅ فلتر الـ Permissions - خلي بس اللي عندهم نفس الـ guard
                $validPermissions = \Spatie\Permission\Models\Permission::whereIn('id', $data['permissions'])
                    ->where('guard_name', $role->guard_name) // ✅ نفس guard الـ Role
                    ->pluck('id')
                    ->toArray();

                $role->syncPermissions($validPermissions);

                // إضافة access dashboard تلقائياً لكل الأدوار
                $dashboardPermission = \Spatie\Permission\Models\Permission::where('name', 'access dashboard')
                    ->where('guard_name', 'web')
                    ->first();
                if ($dashboardPermission && !$role->hasPermissionTo($dashboardPermission)) {
                    $role->givePermissionTo($dashboardPermission);
                }
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
