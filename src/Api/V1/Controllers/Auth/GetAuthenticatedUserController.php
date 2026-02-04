<?php

declare(strict_types=1);

namespace Src\Api\V1\Controllers\Auth;

use OpenApi\Annotations as OA;
use Src\Api\V1\Controllers\Controller;
use App\Http\Resources\Users\UserResource;
use Src\Application\Auth\UseCases\GetAuthenticatedUserUseCase;

/**
 * @OA\Get(
 *     path="/api/v1/auth/me",
 *     tags={"Auth"},
 *     summary="Get authenticated user",
 *     description="Returns the currently authenticated user",
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Authenticated user returned"
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized"
 *     )
 * )
 */
final class GetAuthenticatedUserController extends Controller
{
    public function __invoke(
        GetAuthenticatedUserUseCase $useCase
    ): UserResource {
        $userResponse = $useCase->execute();

        return new UserResource($userResponse);
    }
}
