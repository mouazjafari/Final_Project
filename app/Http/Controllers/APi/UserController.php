<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Http\Services\Api\UserService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    protected $userService;
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    public function show()
    {
        $user = Auth::user();
        return $this->success(new UserResource($user));
    }
    public function update(UpdateUserRequest $request)
    {
        $user = $this->userService->updateInfo($request->validated());
        return $this->success(new UserResource($user), "User Profile Updated successfully !");
    }
    public function destroy()
    {
        $user = Auth::user();
        User::where('id', $user->id)->delete();
        return $this->success("User Profile Deleted successfully !");
    }
}
