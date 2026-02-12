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

    Route::prefix('auth')
        ->as('auth.')
        ->group(function () {

            Route::post('register', [AuthController::class, 'register'])
                ->name('register');

            Route::post('login', [AuthController::class, 'login'])
                ->name('login');
        });

    Route::middleware('auth:sanctum')->group(function () {

        Route::prefix('auth')
            ->as('auth.')
            ->group(function () {

                Route::get('me', [AuthController::class, 'me'])
                    ->name('me');
            });

        Route::prefix('users')
            ->as('users.')
            ->group(function () {
                Route::prefix('profile')
                    ->as('profile.')
                    ->group(function () {

                        Route::get('get', [UserController::class, 'show'])
                            ->name('get');

                        Route::get('status/get', [UserController::class, 'status'])
                            ->name('status.get');

                        Route::post('complete', [UserController::class, 'complete'])
                            ->name('complete');

                        Route::put('update', [UserController::class, 'updateUserProfile'])
                            ->name('update');

                        Route::post('skip', [UserController::class, 'skip'])
                            ->name('skip');
                    });
                Route::prefix('seller-profile')
                    ->as('seller-profile.')
                    ->group(function () {

                        Route::put('update', [UserController::class, 'updateSellerProfile'])
                            ->name('update');
                    });
                Route::post('become-seller', [UserController::class, 'becomeSeller'])
                    ->name('become-seller');
            });

        Route::prefix('products')
            ->as('products.')
            ->group(function () {

                Route::get('list', [ProductController::class, 'index'])
                    ->name('list');

                Route::post('create', [ProductController::class, 'store'])
                    ->name('create');

                Route::get('{product}/get', [ProductController::class, 'show'])
                    ->name('get');

                Route::put('{product}/update', [ProductController::class, 'update'])
                    ->name('update');

                Route::delete('{product}/delete', [ProductController::class, 'destroy'])
                    ->name('delete');
            });
    });
});
