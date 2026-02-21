<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Application\UseCases\Collaborations\GetCollaborationPayoutUseCase;
use App\Application\UseCases\Collaborations\GetCollaborationStatsUseCase;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Collaboration Metrics", description="Traction stats and tier payout based on clicks")
 */
class CollaborationMetricsController extends Controller
{
    private function user(): User
    {
        /** @var User */
        return Auth::user();
    }

    /**
     * @OA\Get(
     *     path="/api/v1/collaborations/{id}/stats",
     *     tags={"Collaboration Metrics"},
     *     summary="Get collaboration traction stats (clicks)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string", format="uuid")),
     *     @OA\Response(response=200, description="Stats returned"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function stats(string $id, GetCollaborationStatsUseCase $useCase): JsonResponse
    {
        $data = $useCase->execute($this->user(), $id);

        return response()->json(['data' => $data]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/collaborations/{id}/payout",
     *     tags={"Collaboration Metrics"},
     *     summary="Get collaboration payout based on tiers (clicks)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string", format="uuid")),
     *     @OA\Response(response=200, description="Payout returned"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function payout(string $id, GetCollaborationPayoutUseCase $useCase): JsonResponse
    {
        $data = $useCase->execute($this->user(), $id);

        return response()->json(['data' => $data]);
    }
}