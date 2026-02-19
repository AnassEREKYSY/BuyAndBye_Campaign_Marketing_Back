<?php

use App\Http\Controllers\Api\V1\AdminUserController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/health', fn () => response()->json([
    'status' => 'ok',
    'message' => 'API is running',
]));

Route::prefix('v1')->group(function () {

    Route::prefix('auth')->middleware('throttle:5,1')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
    });

    Route::middleware('auth:sanctum')->group(function () {

        Route::get('auth/me', [AuthController::class, 'me']);

        Route::prefix('users')->group(function () {
            Route::get('profile', [UserController::class, 'show']);
            Route::put('profile/brand', [UserController::class, 'updateBrandProfile']);
            Route::put('profile/influencer', [UserController::class, 'updateInfluencerProfile']);
        });

        Route::prefix('admin/users')->group(function () {
            Route::get('', [AdminUserController::class, 'index']);
            Route::get('{id}', [AdminUserController::class, 'show']);
            Route::put('{id}', [AdminUserController::class, 'update']);
            Route::post('{id}/suspend', [AdminUserController::class, 'suspend']);
            Route::post('{id}/activate', [AdminUserController::class, 'activate']);
            Route::delete('{id}', [AdminUserController::class, 'delete']);
            Route::post('{id}/restore', [AdminUserController::class, 'restore']);
        });
    });
});