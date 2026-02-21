<?php

use App\Http\Controllers\Api\V1\AdminUserController;
use App\Http\Controllers\Api\V1\ApplicationDecisionController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CampaignApplicationController;
use App\Http\Controllers\Api\V1\CampaignController;
use App\Http\Controllers\Api\V1\CampaignPayoutTierController;
use App\Http\Controllers\Api\V1\CollaborationController;
use App\Http\Controllers\Api\V1\CollaborationMetricsController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\TrackingController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/health', fn () => response()->json([
    'status' => 'ok',
    'message' => 'API is running',
]));

Route::prefix('v1')->group(function () {

    Route::get('t/{code}', [TrackingController::class, 'redirect']);

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

        Route::apiResource('products', ProductController::class);

        Route::get('campaigns', [CampaignController::class, 'index']);
        Route::post('campaigns', [CampaignController::class, 'store']);
        Route::get('campaigns/{campaign}', [CampaignController::class, 'show']);
        Route::put('campaigns/{campaign}', [CampaignController::class, 'update']);
        Route::delete('campaigns/{campaign}', [CampaignController::class, 'destroy']);
        Route::post('campaigns/{campaign}/publish', [CampaignController::class, 'publish']);

        Route::get('campaigns/{campaign}/tiers', [CampaignPayoutTierController::class, 'index']);
        Route::post('campaigns/{campaign}/tiers', [CampaignPayoutTierController::class, 'store']);
        Route::put('tiers/{id}', [CampaignPayoutTierController::class, 'update']);
        Route::delete('tiers/{id}', [CampaignPayoutTierController::class, 'destroy']);

        Route::post('campaigns/{campaign}/apply', [CampaignApplicationController::class, 'apply']);
        Route::get('applications', [CampaignApplicationController::class, 'myApplications']);
        Route::get('brand/campaigns/{campaign}/applications', [CampaignApplicationController::class, 'brandCampaignApplications']);

        Route::post('applications/{id}/shortlist', [ApplicationDecisionController::class, 'shortlist']);
        Route::post('applications/{id}/accept', [ApplicationDecisionController::class, 'accept']);
        Route::post('applications/{id}/reject', [ApplicationDecisionController::class, 'reject']);

        Route::get('collaborations', [CollaborationController::class, 'index']);
        Route::get('collaborations/{id}', [CollaborationController::class, 'show']);
        Route::get('collaborations/{id}/stats', [CollaborationMetricsController::class, 'stats']);
        Route::get('collaborations/{id}/payout', [CollaborationMetricsController::class, 'payout']);

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