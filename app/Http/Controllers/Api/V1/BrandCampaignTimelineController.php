<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Application\UseCases\Analytics\BrandCampaignTimelineUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\TimelineRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Brand Timeline", description="Brand campaign timeline analytics")
 */
class BrandCampaignTimelineController extends Controller
{
    private function user(): User
    {
        /** @var User */
        return Auth::user();
    }

    /**
     * @OA\Get(
     *     path="/api/v1/brand/campaigns/{campaignId}/timeline",
     *     tags={"Brand Timeline"},
     *     summary="Campaign timeline (clicks total + unique)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="campaignId", in="path", required=true, @OA\Schema(type="string", format="uuid")),
     *     @OA\Parameter(name="from", in="query", required=true, @OA\Schema(type="string", format="date", example="2026-02-01")),
     *     @OA\Parameter(name="to", in="query", required=true, @OA\Schema(type="string", format="date", example="2026-02-21")),
     *     @OA\Parameter(name="group", in="query", required=false, @OA\Schema(type="string", enum={"day"}, example="day")),
     *     @OA\Response(response=200, description="Timeline returned"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function timeline(string $campaign, TimelineRequest $request, BrandCampaignTimelineUseCase $useCase): JsonResponse
    {
        Gate::authorize('brand-only');

        $data = $useCase->execute($this->user(), $campaign, $request->from(), $request->to(), $request->group());

        return response()->json(['data' => $data]);
    }
}