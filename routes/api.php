<?php

use Illuminate\Support\Facades\Route;
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


Route::get('/health', function () {
    return response()->json(['status' => 'ok', 'message' => 'API is running']);
});

Route::prefix('v1')->group(function () {
    Route::post('auth/register', RegisterController::class)->name('auth.register');
    Route::post('auth/login', LoginController::class)->name('auth.login');
    Route::post('auth/login-google', LoginWithGoogleController::class)->name('auth.google');

    Route::get('products/get-one/{productId}', GetProductByIdController::class)->name('products.show');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('auth/me', GetAuthenticatedUserController::class)->name('auth.me');
        Route::get('auth/check-buyer', CheckBuyerAuthorizationController::class)->name('auth.check-buyer');
        Route::get('auth/check-seller', CheckSellerAuthorizationController::class)->name('auth.check-seller');
        Route::get('auth/check-admin', CheckAdminAuthorizationController::class)->name('auth.check-admin');
        Route::get('auth/check-seller-or-admin', CheckSellerOrAdminAuthorizationController::class)->name('auth.check-seller-or-admin');

        Route::get('users/profile', GetUserProfileController::class)->name('users.profile.get');
        Route::get('users/profile/status', GetProfileStatusController::class)->name('users.profile.status');
        Route::post('users/profile/complete', CompleteProfileController::class)->name('users.profile.complete');
        Route::post('users/profile/skip', SkipProfileController::class)->name('users.profile.skip');
        Route::put('users/profile', UpdateProfileController::class)->name('users.profile.update');

        Route::post('products/create', CreateProductController::class)->name('products.create');
        Route::get('products/get-mine', GetSellerProductsController::class)->name('products.mine');
        Route::put('products/update/{productId}', UpdateProductController::class)->name('products.update');
        Route::delete('products/delete/{productId}', DeleteProductController::class)->name('products.delete');
    });
});