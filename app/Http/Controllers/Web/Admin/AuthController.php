<?php

namespace App\Http\Controllers\web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Enum\RoleUserEnum;
use App\Http\Requests\LoginUserRequest;
use App\Http\Services\Web\Admin\AuthService as AdminAuthService;
use App\Models\Address;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AdminAuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * معالجة تسجيل الدخول
     */
    public function login(LoginUserRequest $request)
    {
        try {
            $credentials = $request->only('email', 'password');
            $remember = $request->boolean('remember');

            $result = $this->authService->loginAdmin($credentials, $remember);

            if (!$result['success']) {
                return back()
                    ->withErrors(['email' => $result['message']])
                    ->withInput($request->only('email', 'remember'));
            }

            $request->session()->regenerate();

            return redirect()
                ->intended(route('admin.dashboard'));
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage())
                ->withInput($request->only('email', 'remember'));
        }
    }

    public function logout(Request $request)
    {
        $this->authService->logout($request);

        return redirect()
            ->route('admin.login')
            ->with('success', 'تم تسجيل الخروج بنجاح');
    }
    public function showLoginForm()
    {
        return view('login');
    }

    /**
     * تحديث FCM Token للإشعارات
     */
    public function updateFcmToken(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string'
        ]);

        /** @var User $user */
        $user = Auth::user();
        $user->update(['fcm_token' => $request->fcm_token]);

        return response()->json(['success' => true]);
    }
}
