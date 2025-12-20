<?php

namespace App\Http\Services\Web\Admin;

use App\Http\Enum\RoleUserEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log as FacadesLog;

class AuthService
{
    /**
     * Attempt to login admin (wrapper / compatibility)
     *
     * @param array $data
     * @param bool $remember
     * @return array
     */
    public function loginAdmin(array $data, bool $remember = false): array
    {
        // forward to attemptLogin and pass remember flag
        return $this->attemptLogin($data, $remember);
    }

    /**
     * Try to authenticate user and ensure they have admin role
     *
     * @param array $credentials
     * @param bool  $remember
     * @return array
     */
    public function attemptLogin(array $credentials, bool $remember = false): array
    {
        try {
            // Basic validation
            if (empty($credentials['email']) || empty($credentials['password'])) {
                return [
                    'success' => false,
                    'message' => 'البريد الإلكتروني وكلمة المرور مطلوبان'
                ];
            }

            // Attempt authentication
            if (! Auth::attempt($credentials, $remember)) {
                return [
                    'success' => false,
                    'message' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة'
                ];
            }

            $user = Auth::user();

            /** @var \App\Models\User $user */
            if ($user->hasRole(RoleUserEnum::User)) {
                Auth::logout();
                return [
                    'success' => false,
                    'message' => 'غير مسموح بالدخول إلى لوحة التحكم'
                ];
            }
            // -------------------------------------------------------

            // Successful admin login
            return [
                'success' => true,
                'message' => 'تم تسجيل الدخول بنجاح!',
                'user' => $user
            ];
        } catch (\Throwable $e) {
            // Log full error (email may be missing so guard)
            $email = $credentials['email'] ?? null;
            FacadesLog::error('Login error: ' . $e->getMessage(), [
                'email' => $email,
                'ip' => request()->ip(),
                'exception' => $e
            ]);

            return [
                'success' => false,
                'message' => 'حدث خطأ أثناء تسجيل الدخول. يرجى المحاولة مرة أخرى.'
            ];
        }
    }

    /**
     * Logout and properly invalidate session to avoid 419 errors.
     *
     * @param Request $request
     * @return array
     */
    public function logout(Request $request): array
    {
        try {
            Auth::logout();

            // Important: invalidate session and regenerate CSRF token
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return [
                'success' => true,
                'message' => 'تم تسجيل الخروج بنجاح'
            ];
        } catch (\Throwable $e) {
            FacadesLog::error('Logout error: ' . $e->getMessage(), [
                'ip' => request()->ip(),
                'exception' => $e
            ]);
            return [
                'success' => false,
                'message' => 'حدث خطأ أثناء تسجيل الخروج'
            ];
        }
    }
}
