<?php

declare(strict_types=1);

namespace Src\Api\V1\Controllers\Users;

use Src\Api\V1\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Src\Application\Users\UseCases\GetProfileStatusUseCase;

/**
 * @OA\Get(
 *     path="/api/v1/users/profile/status",
 *     tags={"Users"},
 *     summary="Get profile completion status",
 *     description="Returns profile completion status",
 *     security={{"bearerAuth":{}}},
 *
 *     @OA\Response(
 *         response=200,
 *         description="Profile status retrieved",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="completed"),
 *             @OA\Property(property="isProfileComplete", type="boolean", example=true)
 *         )
 *     ),
 *     @OA\Response(response=401, description="Unauthorized")
 * )
 */

class GetProfileStatusController extends Controller
{
    public function __invoke(GetProfileStatusUseCase $useCase): JsonResponse
    {
        $statusResponse = $useCase->execute();

        return response()->json([
            'status' => $statusResponse->status,
            'isProfileComplete' => $statusResponse->isProfileComplete,
        ]);
    }
}
