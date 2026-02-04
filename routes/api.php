<?php

use Src\Api\V1\Controllers\Auth\CheckAdminAuthorizationController;
use Src\Api\V1\Controllers\Auth\CheckBuyerAuthorizationController;
use Src\Api\V1\Controllers\Auth\CheckSellerAuthorizationController;
use Src\Api\V1\Controllers\Auth\CheckSellerOrAdminAuthorizationController;
use Src\Api\V1\Controllers\Auth\GetAuthenticatedUserController;
use Src\Api\V1\Controllers\Auth\LoginController;
use Src\Api\V1\Controllers\Auth\LoginWithGoogleController;
use Src\Api\V1\Controllers\Auth\RegisterController;
use Src\Api\V1\Controllers\Products\CreateProductController;
use Src\Api\V1\Controllers\Products\DeleteProductController;
use Src\Api\V1\Controllers\Products\GetProductByIdController;
use Src\Api\V1\Controllers\Products\GetSellerProductsController;
use Src\Api\V1\Controllers\Products\UpdateProductController;
use Src\Api\V1\Controllers\Users\CompleteProfileController;
use Src\Api\V1\Controllers\Users\GetProfileStatusController;
use Src\Api\V1\Controllers\Users\GetUserProfileController;
use Src\Api\V1\Controllers\Users\SkipProfileController;
use Src\Api\V1\Controllers\Users\UpdateProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Health check endpoint
Route::get('/health', function () {
    return response()->json(['status' => 'ok', 'message' => 'API is running']);
});

Route::prefix('v1')->group(function () {
    // Authentication endpoints - public routes
    Route::post('auth/register', RegisterController::class)->name('auth.register');
    Route::post('auth/login', LoginController::class)->name('auth.login');
    Route::post('auth/google', LoginWithGoogleController::class)->name('auth.google');

    // Product endpoints - public read, protected write
    Route::get('products/{productId}', GetProductByIdController::class)->name('products.show');

    // Protected endpoints - require authentication
    Route::middleware('auth:sanctum')->group(function () {
        // Authentication endpoints
        Route::get('auth/me', GetAuthenticatedUserController::class)->name('auth.me');

        // Role-based authorization check endpoints
        Route::get('auth/check-buyer', CheckBuyerAuthorizationController::class)->name('auth.check-buyer');
        Route::get('auth/check-seller', CheckSellerAuthorizationController::class)->name('auth.check-seller');
        Route::get('auth/check-admin', CheckAdminAuthorizationController::class)->name('auth.check-admin');
        Route::get('auth/check-seller-or-admin', CheckSellerOrAdminAuthorizationController::class)->name('auth.check-seller-or-admin');

        // User profile endpoints
        Route::get('users/profile', GetUserProfileController::class)->name('users.profile.get');
        Route::get('users/profile/status', GetProfileStatusController::class)->name('users.profile.status');
        Route::post('users/profile/complete', CompleteProfileController::class)->name('users.profile.complete');
        Route::post('users/profile/skip', SkipProfileController::class)->name('users.profile.skip');
        Route::put('users/profile', UpdateProfileController::class)->name('users.profile.update');

        // Product endpoints - protected
        Route::post('products', CreateProductController::class)->name('products.create');
        Route::get('products/mine', GetSellerProductsController::class)->name('products.mine');
        Route::put('products/{productId}', UpdateProductController::class)->name('products.update');
        Route::delete('products/{productId}', DeleteProductController::class)->name('products.delete');
    });
});