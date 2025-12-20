<?php

namespace App\Http\Controllers\APi;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Resources\UserResource;
use App\Http\Services\Api\AuthService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    protected $AuthService;
    public function __construct(AuthService $authService)
    {
        $this->AuthService = $authService;
    }

    public function login(LoginUserRequest $request)
    {
        $user = $this->AuthService->loginUser($request->validated());
        return $this->success([new UserResource($user['user']), $user['token']]);
    }
    public function register(RegisterUserRequest $request)
    {
        $user = $this->AuthService->RegisterUser($request->validated());
        return $this->success([new UserResource($user['user']), $user['token']]);
    }
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json("Loged out successfully!", 200);
    }
}
