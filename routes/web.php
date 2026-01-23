<?php

use App\Http\Controllers\Web\Admin\AddressController;
use App\Http\Controllers\Web\Admin\AuthController;
use App\Http\Controllers\Web\Admin\DashboardController;
use App\Http\Controllers\Web\Admin\DesignController;
use App\Http\Controllers\Web\Admin\DesignOptionController;
use App\Http\Controllers\Web\Admin\OrderController;
use App\Http\Controllers\Web\Admin\UserController;
use App\Http\Controllers\Web\Admin\WalletController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// الصفحة الرئيسية
Route::get('/', function () {
    return view('login');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->group(function () {

    /*
    |---------------------------
    | Guest Admin Routes
    |---------------------------
    */
    Route::get('/login', [AuthController::class, 'showLoginForm'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->name('login.submit');

    /*
    |---------------------------
    | Authenticated Admin Routes
    |---------------------------
    */
    Route::middleware(['auth', 'admin'])->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        // Addresses
        Route::get('/dashboard/address', [AddressController::class, 'index'])
            ->name('address');

        // Users
        Route::get('/dashboard/user', [UserController::class, 'index'])
            ->name('users');

        // Design Options
        Route::get('/dashboard/design_options', [DesignOptionController::class, 'index'])
            ->name('design_options');

        Route::post('/design-options', [DesignOptionController::class, 'store'])
            ->name('design_options.store');

        Route::put('/design-options/{id}', [DesignOptionController::class, 'update'])
            ->name('design_options.update');

        Route::delete('/design-options/{id}', [DesignOptionController::class, 'destroy'])
            ->name('design_options.destroy');

        // Designs
        Route::get('/designs', [DesignController::class, 'index'])
            ->name('designs');

        Route::get('/designs/{id}/details', [DesignController::class, 'getDesignDetails'])
            ->name('designs.details');

        // Orders
        Route::get('/orders', [OrderController::class, 'index'])
            ->name('orders.index');

        Route::get('/orders/{id}', [OrderController::class, 'show'])
            ->name('orders.show');

        Route::post('/orders/{id}/status', [OrderController::class, 'updateStatus'])
            ->name('orders.updateStatus');

        // Logout
        Route::post('/logout', [AuthController::class, 'logout'])
            ->name('logout');

        // Wallets Management
        Route::prefix('wallets')->name('wallets.')->group(function () {
            Route::get('/', [WalletController::class, 'index'])
                ->name('index');

            Route::get('/{userId}', [WalletController::class, 'show'])
                ->name('show');

            Route::post('/{userId}/add', [WalletController::class, 'addBalance'])
                ->name('add');

            Route::post('/{userId}/withdraw', [WalletController::class, 'withdrawBalance'])
                ->name('withdraw');
        });
    });
});
Route::get('/stripe/success', function () {
    return view('stripe.success');
})->name('stripe.success');

Route::get('/stripe/cancel', function () {
    return view('stripe.cancel');
})->name('stripe.cancel');
