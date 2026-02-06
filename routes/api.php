<?php

use App\Http\Controllers\APi\AdderssController;
use App\Http\Controllers\APi\AuthController;
use App\Http\Controllers\Api\CouponController;
use App\Http\Controllers\Api\DesignController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\WebhookController;
use App\Http\Controllers\FcmTokenController;
use App\Http\Controllers\NotificationController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/stripe/webhook', [WebhookController::class, 'handleStripeWebhook'])->withoutMiddleware([VerifyCsrfToken::class]);

Route::prefix('user')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::middleware(['auth:api', 'active.user'])->group(function () {
        Route::get('/', [UserController::class, 'show']);
        Route::put('/update', [UserController::class, 'update']);
        Route::delete('/destroy', [UserController::class, 'destroy']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::prefix('address')->group(function () {
            Route::post('/create', [AdderssController::class, 'store']);
            Route::put('/update', [AdderssController::class, 'update']);
            Route::post('/index', [AdderssController::class, 'index']);
            Route::delete('/destroy/{address}', [AdderssController::class, 'destroy']);
        });
        Route::prefix('design')->group(function () {
            Route::post('/', [DesignController::class, 'myDesigns']);
            Route::post('/all', [DesignController::class, 'index']);
            Route::post('/create', [DesignController::class, 'create']);
            Route::post('/update/{design}', [DesignController::class, 'update']);
            Route::delete('/destroy/{design}', [DesignController::class, 'destroy']);
        });
        Route::prefix('order')->group(function () {
            Route::get('/', [OrderController::class, 'show']);
            Route::get('/all', [OrderController::class, 'index']);
            Route::post('/create', [OrderController::class, 'create']);
            Route::post('/update/{designOrder}', [OrderController::class, 'update']);
            Route::post('/cancel/{order}', [OrderController::class, 'cancel']);
            Route::post('{order}/apply-coupon', [CouponController::class, 'apply']);
            Route::post('{order}/remove-coupon', [CouponController::class, 'remove']);
            Route::prefix('review')->group(function () {
                Route::post('/createReview', [ReviewController::class, 'store']);
                Route::post('/updateReview/{review}', [ReviewController::class, 'update']);
                Route::delete('/deleteReview/{review}', [ReviewController::class, 'delete']);
            });
        });
        Route::prefix('payment')->group(function () {
            // Stripe Checkout
            Route::post('/create-checkout/{order}', [PaymentController::class, 'createCheckout']);

            // Wallet Payment
            Route::post('/wallet/{order}', [PaymentController::class, 'payWithWallet']);

            // عرض تفاصيل الدفعة
            Route::get('/{payment}', [PaymentController::class, 'show']);

            Route::post('/createInvoice/{order}', [InvoiceController::class, 'create']);
        });
        Route::prefix('notifications')->group(function () {
            Route::get('/', [NotificationController::class, 'index']);
            Route::get('/unread', [NotificationController::class, 'unread']);
            Route::get('/count', [NotificationController::class, 'count']);
            Route::post('/{id}/read', [NotificationController::class, 'markAsRead']);
            Route::post('/read-all', [NotificationController::class, 'markAllAsRead']);
            Route::delete('/{id}', [NotificationController::class, 'destroy']);
            Route::delete('/', [NotificationController::class, 'destroyAll']);
        });
        Route::prefix('fcm')->group(function () {
            Route::post('/token', [FcmTokenController::class, 'store']);
            Route::delete('/token', [FcmTokenController::class, 'destroy']);
        });
    });
});
