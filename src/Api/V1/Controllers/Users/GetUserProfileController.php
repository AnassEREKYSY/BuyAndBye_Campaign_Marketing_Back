<?php

declare(strict_types=1);

namespace Src\Api\V1\Controllers\Users;

use Src\Api\V1\Controllers\Controller;
use Src\Api\V1\Resources\Users\ProfileResource;
use Src\Application\Users\UseCases\GetUserProfileUseCase;

/**
 * @OA\Get(
 *     path="/api/v1/users/profile",
 *     tags={"Users"},
 *     summary="Get user profile",
 *     description="Retrieve authenticated user profile",
 *     security={{"bearerAuth":{}}},
 *
 *     @OA\Response(
 *         response=200,
 *         description="Profile retrieved successfully"
 *     ),
 *     @OA\Response(response=401, description="Unauthorized")
 * )
 */

class GetUserProfileController extends Controller
{
    public function __invoke(GetUserProfileUseCase $useCase): ProfileResource
    {
        $profileResponse = $useCase->execute();

        return new ProfileResource($profileResponse);
    }
}
