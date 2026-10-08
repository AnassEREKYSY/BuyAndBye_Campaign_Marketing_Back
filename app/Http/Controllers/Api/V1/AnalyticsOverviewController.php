<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Application\UseCases\Analytics\BrandAnalyticsOverviewUseCase;
use App\Application\UseCases\Analytics\InfluencerAnalyticsOverviewUseCase;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Analytics", description="Cross-campaign analytics overviews")
 */
class AnalyticsOverviewController extends Controller
{
    private function user(): User
    {
        /** @var User */
        return Auth::user();
    }

    /**
     * @OA\Get(
     *     path="/api/v1/brand/analytics/overview",
     *     tags={"Analytics"},
     *     summary="Brand analytics across all campaigns",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="days", in="query", required=false, @OA\Schema(type="integer", enum={7,30,90})),
     *     @OA\Response(response=200, description="Overview returned")
     * )
     */
    public function brand(Request $request, BrandAnalyticsOverviewUseCase $useCase): JsonResponse
    {
        Gate::authorize('brand-only');

        return response()->json(['data' => $useCase->execute($this->user(), (int) $request->query('days', 30))]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/influencer/analytics/overview",
     *     tags={"Analytics"},
     *     summary="Creator analytics: links, promo codes, clicks and earnings",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="days", in="query", required=false, @OA\Schema(type="integer", enum={7,30,90})),
     *     @OA\Response(response=200, description="Overview returned")
     * )
     */
    public function influencer(Request $request, InfluencerAnalyticsOverviewUseCase $useCase): JsonResponse
    {
        Gate::authorize('influencer-only');

        return response()->json(['data' => $useCase->execute($this->user(), (int) $request->query('days', 30))]);
    }
}
