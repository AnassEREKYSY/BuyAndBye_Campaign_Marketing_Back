<?php

use App\Http\Controllers\Api\V1\Auth\CheckAdminAuthorizationController;
use App\Http\Controllers\Api\V1\Auth\CheckBuyerAuthorizationController;
use App\Http\Controllers\Api\V1\Auth\CheckSellerAuthorizationController;
use App\Http\Controllers\Api\V1\Auth\CheckSellerOrAdminAuthorizationController;
use App\Http\Controllers\Api\V1\Auth\GetAuthenticatedUserController;
use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\Auth\LoginWithGoogleController;
use App\Http\Controllers\Api\V1\Auth\RegisterController;
use App\Http\Controllers\Api\V1\Products\CreateProductController;
use App\Http\Controllers\Api\V1\Products\DeleteProductController;
use App\Http\Controllers\Api\V1\Products\GetProductByIdController;
use App\Http\Controllers\Api\V1\Products\GetSellerProductsController;
use App\Http\Controllers\Api\V1\Products\UpdateProductController;
use App\Http\Controllers\Api\V1\Users\CompleteProfileController;
use App\Http\Controllers\Api\V1\Users\GetProfileStatusController;
use App\Http\Controllers\Api\V1\Users\GetUserProfileController;
use App\Http\Controllers\Api\V1\Users\SkipProfileController;
use App\Http\Controllers\Api\V1\Users\UpdateProfileController;
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
