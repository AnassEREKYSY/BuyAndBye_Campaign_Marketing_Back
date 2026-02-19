<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Application\UseCases\Campaign\CreateCampaignUseCase;
use App\Application\UseCases\Campaign\DeleteCampaignUseCase;
use App\Application\UseCases\Campaign\ListCampaignsUseCase;
use App\Application\UseCases\Campaign\PublishCampaignUseCase;
use App\Application\UseCases\Campaign\UpdateCampaignUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCampaignRequest;
use App\Http\Requests\UpdateCampaignRequest;
use App\Http\Resources\CampaignResource;
use App\Models\Campaign;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Campaigns", description="Campaigns management")
 */
class CampaignController extends Controller
{
    private function user(): User
    {
        /** @var User */
        return Auth::user();
    }

    /**
     * @OA\Get(
     *     path="/api/v1/campaigns",
     *     tags={"Campaigns"},
     *     summary="List campaigns (brand sees own; influencer sees all)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer", example=1)),
     *     @OA\Parameter(name="size", in="query", required=false, @OA\Schema(type="integer", example=20)),
     *     @OA\Parameter(name="status", in="query", required=false, @OA\Schema(type="string", enum={"draft","published","closed"})),
     *     @OA\Response(response=200, description="Campaigns list"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function index(ListCampaignsUseCase $useCase): AnonymousResourceCollection
    {
        $page = (int) request()->query('page', 1);
        $size = (int) request()->query('size', 20);
        $status = request()->query('status');

        $campaigns = $useCase->execute($this->user(), $page, $size, $status);

        return CampaignResource::collection($campaigns);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/campaigns",
     *     tags={"Campaigns"},
     *     summary="Create a campaign (brand only)",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"product_id","title","commission_type","commission_value"},
     *             @OA\Property(property="product_id", type="string", format="uuid"),
     *             @OA\Property(property="title", type="string", example="Campaign for Product A"),
     *             @OA\Property(property="objective", type="string", example="Increase sales"),
     *             @OA\Property(property="commission_type", type="string", enum={"percent","fixed"}, example="percent"),
     *             @OA\Property(property="commission_value", type="number", format="float", example=10),
     *             @OA\Property(property="budget", type="number", format="float", example=2000),
     *             @OA\Property(property="start_at", type="string", format="date-time"),
     *             @OA\Property(property="end_at", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Campaign created"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(CreateCampaignRequest $request, CreateCampaignUseCase $useCase): JsonResponse
    {
        Gate::authorize('brand-only');

        $campaign = $useCase->execute($this->user(), $request->toDto());
        $campaign->load('product');

        return (new CampaignResource($campaign))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/campaigns/{id}",
     *     tags={"Campaigns"},
     *     summary="Get a campaign by id",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string", format="uuid")),
     *     @OA\Response(response=200, description="Campaign returned"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function show(Campaign $campaign): CampaignResource
    {
        $campaign->load('product');

        if ($this->user()->role->isBrand() && $campaign->brand_id !== $this->user()->id) {
            abort(403, 'Forbidden');
        }

        return new CampaignResource($campaign);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/campaigns/{id}",
     *     tags={"Campaigns"},
     *     summary="Update a campaign (brand only)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string", format="uuid")),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="objective", type="string"),
     *             @OA\Property(property="commission_type", type="string", enum={"percent","fixed"}),
     *             @OA\Property(property="commission_value", type="number", format="float"),
     *             @OA\Property(property="budget", type="number", format="float"),
     *             @OA\Property(property="start_at", type="string", format="date-time"),
     *             @OA\Property(property="end_at", type="string", format="date-time"),
     *             @OA\Property(property="status", type="string", enum={"draft","published","closed"})
     *         )
     *     ),
     *     @OA\Response(response=200, description="Campaign updated"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Not found"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function update(string $id, UpdateCampaignRequest $request, UpdateCampaignUseCase $useCase): CampaignResource
    {
        Gate::authorize('brand-only');

        $campaign = $useCase->execute($this->user(), $id, $request->toDto());
        $campaign->load('product');

        return new CampaignResource($campaign);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/campaigns/{id}/publish",
     *     tags={"Campaigns"},
     *     summary="Publish a campaign (brand only)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string", format="uuid")),
     *     @OA\Response(response=200, description="Campaign published"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function publish(string $id, PublishCampaignUseCase $useCase): CampaignResource
    {
        Gate::authorize('brand-only');

        $campaign = $useCase->execute($this->user(), $id);
        $campaign->load('product');

        return new CampaignResource($campaign);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/campaigns/{id}",
     *     tags={"Campaigns"},
     *     summary="Delete a campaign (brand only)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string", format="uuid")),
     *     @OA\Response(response=200, description="Campaign deleted"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function destroy(string $id, DeleteCampaignUseCase $useCase): JsonResponse
    {
        Gate::authorize('brand-only');

        $useCase->execute($this->user(), $id);

        return response()->json(['success' => true]);
    }
}