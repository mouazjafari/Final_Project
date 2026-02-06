<?php

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\CheckUserActive;
use App\Http\Middleware\DashboardAccessMiddleware;
use App\Http\Middleware\SetLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Session\TokenMismatchException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => AdminMiddleware::class,
            'dashboard.access' => DashboardAccessMiddleware::class,
            'active.user' => CheckUserActive::class,
        ]);
        $middleware->web(append: [
            SetLocale::class,
        ]);

        // تحديد صفحة تسجيل الدخول الافتراضية
        $middleware->redirectGuestsTo(fn () => route('admin.login'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // معالجة انتهاء الجلسة والـ CSRF token
        $exceptions->render(function (TokenMismatchException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Session expired. Please login again.'], 419);
            }

            // كل الطلبات تروح لـ admin.login
            return redirect()->route('admin.login')
                ->with('error', app()->getLocale() == 'ar'
                    ? 'انتهت صلاحية الجلسة. الرجاء تسجيل الدخول مرة أخرى.'
                    : 'Session expired. Please login again.');
        });

        // معالجة عدم المصادقة
        $exceptions->render(function (AuthenticationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }

            // كل الطلبات تروح لـ admin.login
            return redirect()->guest(route('admin.login'));
        });
    })->create();
