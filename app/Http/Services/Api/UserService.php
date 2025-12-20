<?php

namespace App\Http\Services\Api;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UserService
{
    public function showUserProfile()
    {
        return Auth::user();
    }
    public function updateInfo(array $data)
    {
        try {
            DB::beginTransaction();

            /** @var \App\Models\User $user */
            $user = Auth::user();

            if (!$user) {
                DB::rollBack();
                return false;
            }
            if (isset($data['profile_image'])) {
                if ($user->profile_image) {
                    Storage::disk('public')->delete($user->profile_image);
                }

                $data['profile_image'] = $data['profile_image']->storeAs(
                    'users',
                    $user->email . '.jpg',
                    'public'
                );
                unset($data['profile_image']);
            }

            // معالجة الباسورد
            if (isset($data['password'])) {
                $data['password'] = bcrypt($data['password']);
            }

            // باقي الكود...
            $user->fill($data);
            $user->save();

            DB::commit();
            return $user;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }
    public function deleteProfile()
    {
        $user = Auth::delete();
    }
}
