<?php

declare(strict_types=1);

namespace Src\Api\V1\Controllers\Users;

use Src\Api\V1\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Src\Application\Users\UseCases\SkipProfileUseCase;

/**
 * @OA\Post(
 *     path="/api/v1/users/profile/skip",
 *     tags={"Users"},
 *     summary="Skip profile completion",
 *     description="Skip profile completion step",
 *     security={{"bearerAuth":{}}},
 *
 *     @OA\Response(response=200, description="Profile skipped successfully"),
 *     @OA\Response(response=401, description="Unauthorized")
 * )
 */


class SkipProfileController extends Controller
{
    public function __invoke(SkipProfileUseCase $useCase): JsonResponse
    {
        $useCase->execute();

        return response()->json(['message' => 'Profile skipped successfully']);
    }
}
