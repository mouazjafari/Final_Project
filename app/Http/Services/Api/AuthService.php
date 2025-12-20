<?php

namespace App\Http\Services\Api;

use App\Exceptions\GeneralException;
use App\Http\Controllers\Controller;
use App\Http\Enum\RoleUserEnum;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthService extends Controller
{
    public function RegisterUser(array $data)
    {
        DB::beginTransaction();
        try {
            $credentials = [
                'email' => $data['email'],
                'password' => $data['password'],
            ];

            if (Auth::attempt($credentials)) {
                throw new GeneralException("you are already have an account");
            } else {
                if (isset($data['profile_image'])) {
                    $relativePath = $data['profile_image']->storeAs('users', $data['email'] . '.jpg', 'public');
                    $data['profile_image'] = $relativePath;
                }
                $user = User::create($data);
                $user->assignRole(RoleUserEnum::User);
                $token = $user->createToken('api')->plainTextToken;
                if (! $user->hasRole(RoleUserEnum::User)) {
                    throw new \Exception("Failed to assign role to user");
                }
                if ($user->hasRole('user')) {
                    DB::commit();
                    return [
                        'user' => $user,
                        'token' => $token,
                    ];
                }
            }
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    public function loginUser(array $data)
    {
        if (!Auth::attempt($data)) {
            throw new GeneralException('The credentials is invalid !');
        }
        $user = User::where('email', $data['email'])->Role('user')->first();
        if ($user) {
            $token = $user->createToken('api')->plainTextToken;
            return [
                'user' => $user,
                'token' => $token,
            ];
        } else {
            throw new GeneralException("You dont have a User Role !");
        }
    }
}
