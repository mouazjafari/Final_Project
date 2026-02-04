<?php

namespace Database\Seeders;

use App\Http\Enum\RoleUserEnum;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->permissions() as $guard => $names) {
            $this->seed_for_guard($guard, $names);
        }

        // استخدام firstOrCreate بدلاً من create لتجنب الأخطاء
        $userRole = Role::firstOrCreate(
            ['name' => RoleUserEnum::User->value, 'guard_name' => 'api']
        );
        $userRole->syncPermissions(
            [
                'create profile',
                'update profile',
                'delete profile',
                'add address',
                'edit address',
                'delete address',
                'view my designs',
                'view designs',
                'create design',
                'update design',
                'delete design',
                'view order',
                'create order',
                'update order',
                'cancel order',
                'apply coupon',
                'view transactions',
                'leave reviews',
                'view notifications',
            ]
        );

        $AdminRole = Role::firstOrCreate(
            ['name' => RoleUserEnum::Admin->value, 'guard_name' => 'web']
        );
        $AdminRole->syncPermissions(
            [
                'access dashboard',
                'View all users',
                'create users',
                'assign roles to users',
                'disable/delete accounts',
                'view orders',
                'change status orders',
                'view designs',
                'edit designs',
                'delete designs',
                'view design options',
                'create design option',
                'update design option',
                'delete design option',
                'add to wallet',
                'withdraw from wallet',
                'view coupons',
                'create coupons',
                'edit coupons',
                'delete coupons',
                'manage design options',
                'view reviews',
                'control reviews',
                'send notification'
            ]
        );

        $SuperAdminRole = Role::firstOrCreate(
            ['name' => RoleUserEnum::SuperAdmin->value, 'guard_name' => 'web']
        );
        $SuperAdminRole->syncPermissions(
            Permission::where('guard_name', 'web')->get()
        );
    }
    public function permissions()
    {
        return [
            'api' => [
                //--------------User
                'create profile',
                'update profile',
                'delete profile',
                'add address',
                'edit address',
                'delete address',
                'view my designs',
                'view designs',
                'view design options',
                'create design',
                'update design',
                'delete design',
                'view order',
                'create order',
                'update order',
                'cancel order',
                'apply coupon',
                'view transactions',
                'leave reviews',
                'view notifications',
            ],
            'web' => [
                'access dashboard',
                'View all users',
                'create users',
                'assign roles to users',
                'disable/delete accounts',
                'view orders',
                'change status orders',
                'view designs',
                'view design options',
                'create design option',
                'update design option',
                'delete design option',
                'edit designs',
                'delete designs',
                'add to wallet',
                'withdraw from wallet',
                'view coupons',
                'create coupons',
                'edit coupons',
                'delete coupons',
                'manage design options',
                'view reviews',
                'control reviews',
                'view permissions',
                'create permissions',
                'delete permissions',
                'view roles',
                'create roles',
                'edit roles',
                'delete roles',
                'send notification'
            ]
        ];
    }
    public function seed_for_guard(string $guard, array $names)
    {
        foreach ($names as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => $guard]);
        }
    }
}
