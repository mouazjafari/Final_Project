<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class DashboardAccessMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // تحقق إذا المستخدم مسجل دخول
        if (!Auth::check()) {
            return redirect()->route('admin.login')
                ->with('error', 'يجب تسجيل الدخول أولاً');
        }

        $user = Auth::user();

        // تحقق إذا المستخدم لديه صلاحية الوصول للـ dashboard
        /** @var \App\Models\User $user */
        if (!$user->hasPermissionTo('access dashboard', 'web')) {
            Auth::logout();
            return redirect()->route('admin.login')
                ->with('error', 'ليس لديك صلاحية الوصول إلى لوحة التحكم!');
        }

        return $next($request);
    }
}
