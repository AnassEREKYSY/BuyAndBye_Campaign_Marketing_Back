<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/health', fn () => response()->json([
    'status' => 'ok',
    'message' => 'API is running',
]));

Route::prefix('v1')->group(function () {

    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
    });

    Route::middleware('auth:sanctum')->group(function () {

        Route::get('auth/me', [AuthController::class, 'me']);

        Route::prefix('users')->group(function () {

            Route::get('profile', [UserController::class, 'show']);
            Route::get('profile/status', [UserController::class, 'status']);
            Route::post('profile/complete', [UserController::class, 'complete']);
            Route::put('profile', [UserController::class, 'updateUserProfile']);
            Route::put('seller-profile', [UserController::class, 'updateSellerProfile']);
            Route::post('become-seller', [UserController::class, 'becomeSeller']);
        });

        Route::apiResource('products', ProductController::class);
    });
});