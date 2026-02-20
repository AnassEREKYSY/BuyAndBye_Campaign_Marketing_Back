<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Application\UseCases\CampaignApplications\ApplyToCampaignUseCase;
use App\Application\UseCases\CampaignApplications\ListBrandCampaignApplicationsUseCase;
use App\Application\UseCases\CampaignApplications\ListInfluencerApplicationsUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\ApplyToCampaignRequest;
use App\Http\Resources\CampaignApplicationResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Applications", description="Influencer applications to campaigns")
 */
class CampaignApplicationController extends Controller
{
    private function user(): User
    {
        /** @var User */
        return Auth::user();
    }

    /**
     * @OA\Post(
     *     path="/api/v1/campaigns/{campaignId}/apply",
     *     tags={"Applications"},
     *     summary="Apply to a published campaign (influencer only)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="campaignId", in="path", required=true, @OA\Schema(type="string", format="uuid")),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Hi, I can promote this to my audience...")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Application created"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Campaign not found"),
     *     @OA\Response(response=409, description="Already applied or campaign not published"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function apply(string $campaign, ApplyToCampaignRequest $request, ApplyToCampaignUseCase $useCase): JsonResponse
    {
        Gate::authorize('influencer-only');

        $application = $useCase->execute($this->user(), $campaign, $request->toDto());

        return (new CampaignApplicationResource($application))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/applications",
     *     tags={"Applications"},
     *     summary="List my applications (influencer only)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer", example=1)),
     *     @OA\Parameter(name="size", in="query", required=false, @OA\Schema(type="integer", example=20)),
     *     @OA\Response(response=200, description="Applications list"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function myApplications(ListInfluencerApplicationsUseCase $useCase): AnonymousResourceCollection
    {
        Gate::authorize('influencer-only');

        $page = (int) request()->query('page', 1);
        $size = (int) request()->query('size', 20);

        $apps = $useCase->execute($this->user(), $page, $size);

        return CampaignApplicationResource::collection($apps);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/brand/campaigns/{campaignId}/applications",
     *     tags={"Applications"},
     *     summary="List applications for a brand campaign (brand only)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="campaignId", in="path", required=true, @OA\Schema(type="string", format="uuid")),
     *     @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer", example=1)),
     *     @OA\Parameter(name="size", in="query", required=false, @OA\Schema(type="integer", example=20)),
     *     @OA\Response(response=200, description="Campaign applications list"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Campaign not found")
     * )
     */
    public function brandCampaignApplications(string $campaign, ListBrandCampaignApplicationsUseCase $useCase): AnonymousResourceCollection
    {
        Gate::authorize('brand-only');

        $page = (int) request()->query('page', 1);
        $size = (int) request()->query('size', 20);

        $apps = $useCase->execute($this->user(), $campaign, $page, $size);

        return CampaignApplicationResource::collection($apps);
    }
}