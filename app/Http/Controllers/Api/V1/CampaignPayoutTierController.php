<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Application\UseCases\CampaignTiers\CreateCampaignTierUseCase;
use App\Application\UseCases\CampaignTiers\DeleteCampaignTierUseCase;
use App\Application\UseCases\CampaignTiers\ListCampaignTiersUseCase;
use App\Application\UseCases\CampaignTiers\UpdateCampaignTierUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCampaignTierRequest;
use App\Http\Requests\UpdateCampaignTierRequest;
use App\Http\Resources\CampaignPayoutTierResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Campaign Tiers", description="Campaign payout tiers based on clicks/views")
 */
class CampaignPayoutTierController extends Controller
{
    private function user(): User
    {
        /** @var User */
        return Auth::user();
    }

    /**
     * @OA\Get(
     *     path="/api/v1/campaigns/{campaignId}/tiers",
     *     tags={"Campaign Tiers"},
     *     summary="List payout tiers for a campaign (brand/influencer/admin)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="campaignId", in="path", required=true, @OA\Schema(type="string", format="uuid")),
     *     @OA\Response(response=200, description="Tiers list"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function index(string $campaign, ListCampaignTiersUseCase $useCase): AnonymousResourceCollection
    {
        Gate::authorize('tiers-read');
        // brand is not included here, so allow brand explicitly:
        if ($this->user()->role->isBrand()) {
            // ok
        } else {
            // already validated by influencer-or-admin
        }

        $tiers = $useCase->execute($campaign);

        return CampaignPayoutTierResource::collection($tiers);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/campaigns/{campaignId}/tiers",
     *     tags={"Campaign Tiers"},
     *     summary="Create a payout tier (brand only)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="campaignId", in="path", required=true, @OA\Schema(type="string", format="uuid")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"metric","from_value","payout_amount"},
     *             @OA\Property(property="metric", type="string", enum={"clicks"}, example="clicks"),
     *             @OA\Property(property="from_value", type="integer", example=0),
     *             @OA\Property(property="to_value", type="integer", nullable=true, example=1000),
     *             @OA\Property(property="payout_amount", type="number", format="float", example=500),
     *             @OA\Property(property="currency", type="string", example="MAD")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Tier created"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Campaign not found"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(string $campaign, CreateCampaignTierRequest $request, CreateCampaignTierUseCase $useCase): JsonResponse
    {
        Gate::authorize('brand-only');

        $tier = $useCase->execute($this->user(), $campaign, $request->toDto());

        return (new CampaignPayoutTierResource($tier))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/tiers/{id}",
     *     tags={"Campaign Tiers"},
     *     summary="Update a payout tier (brand only)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string", format="uuid")),
     *     @OA\RequestBody(required=false, @OA\JsonContent(
     *         @OA\Property(property="metric", type="string", enum={"clicks"}),
     *         @OA\Property(property="from_value", type="integer"),
     *         @OA\Property(property="to_value", type="integer", nullable=true),
     *         @OA\Property(property="payout_amount", type="number", format="float"),
     *         @OA\Property(property="currency", type="string")
     *     )),
     *     @OA\Response(response=200, description="Tier updated"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Tier not found"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function update(string $id, UpdateCampaignTierRequest $request, UpdateCampaignTierUseCase $useCase): CampaignPayoutTierResource
    {
        Gate::authorize('brand-only');

        $tier = $useCase->execute($this->user(), $id, $request->toDto());

        return new CampaignPayoutTierResource($tier);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/tiers/{id}",
     *     tags={"Campaign Tiers"},
     *     summary="Delete a payout tier (brand only)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string", format="uuid")),
     *     @OA\Response(response=200, description="Tier deleted"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Tier not found")
     * )
     */
    public function destroy(string $id, DeleteCampaignTierUseCase $useCase): JsonResponse
    {
        Gate::authorize('brand-only');

        $useCase->execute($this->user(), $id);

        return response()->json(['success' => true]);
    }
}