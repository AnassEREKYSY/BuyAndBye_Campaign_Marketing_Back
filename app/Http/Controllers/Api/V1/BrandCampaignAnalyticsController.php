<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Application\UseCases\Analytics\BrandCampaignSummaryUseCase;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Brand Dashboard", description="Brand analytics endpoints")
 */
class BrandCampaignAnalyticsController extends Controller
{
    private function user(): User
    {
        /** @var User */
        return Auth::user();
    }

    /**
     * @OA\Get(
     *     path="/api/v1/brand/campaigns/{campaignId}/summary",
     *     tags={"Brand Dashboard"},
     *     summary="Get campaign summary (brand only)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="campaignId", in="path", required=true, @OA\Schema(type="string", format="uuid")),
     *     @OA\Response(response=200, description="Summary returned"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function summary(string $campaign, BrandCampaignSummaryUseCase $useCase): JsonResponse
    {
        Gate::authorize('brand-only');

        $data = $useCase->execute($this->user(), $campaign);

        return response()->json(['data' => $data]);
    }
}