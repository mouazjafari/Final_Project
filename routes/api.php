<?php

use App\Http\Controllers\APi\AdderssController;
use App\Http\Controllers\APi\AuthController;
use App\Http\Controllers\Api\DesignController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::prefix('user')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::middleware('auth:api')->group(function () {
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
        });
        Route::prefix('payment')->group(function () {
            Route::post('/create-intent/{order}', [PaymentController::class, 'createPaymentIntent']);
            Route::post('/confirm', [PaymentController::class, 'confirmPayment']);
            Route::post('/wallet/{order}', [PaymentController::class, 'payWithWallet']);
            Route::get('/{payment}', [PaymentController::class, 'show']);
        });
    });
});
