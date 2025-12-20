<?php

namespace App\Http\Middleware;

use App\Http\Enum\RoleUserEnum;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // تحقق إذا المستخدم مسجل دخول
        if (!Auth::check()) {
            return redirect()->route('admin.login')
                ->with('error', 'يجب تسجيل الدخول أولاً');
        }

        $user = Auth::user();

        // تحقق إذا المستخدم Admin (قارن مع string مباشرة)
        /** @var \App\Models\User $user */
        if ($user->hasRole(RoleUserEnum::User)) {
            Auth::logout();
            return redirect()->route('admin.login')
                ->with('error', 'ليس لديك صلاحيات الدخول كمدير!');
        }

        return $next($request);
    }
}
