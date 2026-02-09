<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/health', fn () => response()->json(['status' => 'ok', 'message' => 'API is running'])
);

Route::prefix('v1')->group(function () {
    // Auth routes (public)
    Route::post('auth/register', [AuthController::class, 'register'])->name('auth.register');
    Route::post('auth/login', [AuthController::class, 'login'])->name('auth.login');

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        // Auth
        Route::get('auth/me', [AuthController::class, 'me'])->name('auth.me');

        // Profile (singular resource)
        Route::get('profile', [UserController::class, 'show']);
        Route::put('profile', [UserController::class, 'update']);
        Route::get('profile/status', [UserController::class, 'status']);
        Route::post('profile/complete', [UserController::class, 'complete']);
        Route::post('profile/skip', [UserController::class, 'skip']);
        Route::post('become-seller', [UserController::class, 'becomeSeller']);

        // Products (RESTful resource)
        Route::apiResource('products', ProductController::class);
    });
});
