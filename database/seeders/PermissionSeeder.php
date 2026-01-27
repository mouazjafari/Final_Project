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
        $userRole = Role::create(
            ['name' => RoleUserEnum::User, 'guard_name' => 'api']
        );
        $userRole->givePermissionTo(
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
        $AdminRole = Role::create(
            ['name' => RoleUserEnum::Admin, 'guard_name' => 'web']
        );
        $AdminRole->givePermissionTo(
            [
                'View all users',
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
        $SuperAdminRole = Role::create(
            ['name' => RoleUserEnum::SuperAdmin, 'guard_name' => 'web']
        );
        $SuperAdminRole->givePermissionTo(
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
                'View all users',
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
