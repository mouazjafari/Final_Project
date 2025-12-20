<?php

namespace Database\Seeders;

use App\Http\Enum\RoleUserEnum;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // User للـ API
        $apiUser = User::create([
            'name' => 'User',
            'email' => 'user@gmail.com',
            'password' => Hash::make('123456'),
        ]);

        $userRoleId = DB::table('roles')
            ->where('name', RoleUserEnum::User->value)
            ->where('guard_name', 'api')
            ->value('id');

        DB::table('model_has_roles')->insert([
            'role_id' => $userRoleId,
            'model_type' => User::class,
            'model_id' => $apiUser->id,
        ]);

        // Admin للـ Web
        $adminUser = User::create([
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('123456')
        ]);

        $adminRoleId = DB::table('roles')
            ->where('name', RoleUserEnum::Admin->value)
            ->where('guard_name', 'web')
            ->value('id');

        DB::table('model_has_roles')->insert([
            'role_id' => $adminRoleId,
            'model_type' => User::class,
            'model_id' => $adminUser->id,
        ]);

        // Super Admin للـ Web
        $superAdminUser = User::create([
            'name' => 'super admin',
            'email' => 'sadmin@gmail.com',
            'password' => Hash::make('123456')
        ]);

        $superAdminRoleId = DB::table('roles')
            ->where('name', RoleUserEnum::SuperAdmin->value)
            ->where('guard_name', 'web')
            ->value('id');

        DB::table('model_has_roles')->insert([
            'role_id' => $superAdminRoleId,
            'model_type' => User::class,
            'model_id' => $superAdminUser->id,
        ]);
    }
}
