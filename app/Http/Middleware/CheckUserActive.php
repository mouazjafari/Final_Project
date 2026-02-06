<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserActive
{
    /**
     * التحقق من أن المستخدم مفعّل
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // إذا المستخدم مسجل دخول وحسابه معطّل
        if ($user && !$user->is_active) {
            // إذا كان الطلب من الـ API
            if ($request->expectsJson()) {
                // حذف الـ tokens إذا كان Sanctum
                if (method_exists($user, 'tokens')) {
                    $user->tokens()->delete();
                }

                return response()->json([
                    'success' => false,
                    'message' => app()->getLocale() == 'ar'
                        ? 'تم تعطيل حسابك. الرجاء التواصل مع الإدارة.'
                        : 'Your account has been deactivated. Please contact support.'
                ], 403);
            }

            // إذا كان الطلب من الويب
            Auth::logout();
            return redirect()->route('admin.login')
                ->with('error', app()->getLocale() == 'ar'
                    ? 'تم تعطيل حسابك. الرجاء التواصل مع الإدارة.'
                    : 'Your account has been deactivated. Please contact support.');
        }

        return $next($request);
    }
}
