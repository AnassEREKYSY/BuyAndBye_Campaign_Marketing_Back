<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Application\UseCases\Profile\GetInfluencerPublicProfileUseCase;
use App\Http\Controllers\Controller;
use App\Http\Resources\InfluencerPublicProfileResource;
use Illuminate\Support\Facades\Gate;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Brand Influencers", description="Brand access to influencer public profiles")
 */
class BrandInfluencerController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/brand/influencers/{id}",
     *     tags={"Brand Influencers"},
     *     summary="Get influencer public profile (brand only)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string", format="uuid")),
     *     @OA\Response(response=200, description="Influencer profile returned"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function show(string $id, GetInfluencerPublicProfileUseCase $useCase): InfluencerPublicProfileResource
    {
        Gate::authorize('brand-only');

        $profile = $useCase->execute($id);

        return new InfluencerPublicProfileResource($profile);
    }
}