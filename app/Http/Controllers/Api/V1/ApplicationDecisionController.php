<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Application\UseCases\CampaignApplications\AcceptFinalApplicationUseCase;
use App\Application\UseCases\CampaignApplications\RejectApplicationUseCase;
use App\Application\UseCases\CampaignApplications\ShortlistApplicationUseCase;
use App\Http\Controllers\Controller;
use App\Http\Resources\CampaignApplicationResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Applications - Decisions", description="Brand decisions on influencer applications")
 */
class ApplicationDecisionController extends Controller
{
    private function user(): User
    {
        /** @var User */
        return Auth::user();
    }

    /**
     * @OA\Post(
     *     path="/api/v1/applications/{id}/shortlist",
     *     tags={"Applications - Decisions"},
     *     summary="Shortlist an application (brand only)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string", format="uuid")),
     *     @OA\Response(response=200, description="Application shortlisted"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Not found"),
     *     @OA\Response(response=409, description="Invalid state / campaign finalized")
     * )
     */
    public function shortlist(string $id, ShortlistApplicationUseCase $useCase): CampaignApplicationResource
    {
        Gate::authorize('brand-only');

        $application = $useCase->execute($this->user(), $id);

        return new CampaignApplicationResource($application);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/applications/{id}/accept",
     *     tags={"Applications - Decisions"},
     *     summary="Accept an application as final candidate (brand only). Auto-rejects others in pending/shortlisted.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string", format="uuid")),
     *     @OA\Response(response=200, description="Application accepted"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Not found"),
     *     @OA\Response(response=409, description="Invalid state / campaign finalized")
     * )
     */
    public function accept(string $id, AcceptFinalApplicationUseCase $useCase): CampaignApplicationResource
    {
        Gate::authorize('brand-only');

        $application = $useCase->execute($this->user(), $id);

        return new CampaignApplicationResource($application);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/applications/{id}/reject",
     *     tags={"Applications - Decisions"},
     *     summary="Reject an application (brand only)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string", format="uuid")),
     *     @OA\Response(response=200, description="Application rejected"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Not found"),
     *     @OA\Response(response=409, description="Invalid state / campaign finalized")
     * )
     */
    public function reject(string $id, RejectApplicationUseCase $useCase): CampaignApplicationResource
    {
        Gate::authorize('brand-only');

        $application = $useCase->execute($this->user(), $id);

        return new CampaignApplicationResource($application);
    }
}