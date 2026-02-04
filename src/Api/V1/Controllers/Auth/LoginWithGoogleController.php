<?php

declare(strict_types=1);

namespace Src\Api\V1\Controllers\Auth;

use OpenApi\Annotations as OA;
use Src\Api\V1\Controllers\Controller;
use Src\Api\V1\Requests\Auth\GoogleLoginRequest;
use Src\Api\V1\Resources\Auth\AuthResource;
use Src\Application\Auth\DTOs\GoogleLoginRequest as GoogleLoginDTO;
use Src\Application\Auth\UseCases\LoginWithGoogleUseCase;

/**
 * @OA\Post(
 *     path="/api/v1/auth/google",
 *     tags={"Auth"},
 *     summary="Login with Google",
 *     description="Authenticates a user using a Google ID token",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"idToken"},
 *             @OA\Property(property="idToken", type="string")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Authenticated successfully via Google"
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Invalid Google token"
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error"
 *     )
 * )
 */
final class LoginWithGoogleController extends Controller
{
    public function __invoke(
        GoogleLoginRequest $request,
        LoginWithGoogleUseCase $useCase
    ): AuthResource {
        $dto = new GoogleLoginDTO(
            idToken: $request->validated('idToken'),
        );

        $authResponse = $useCase->execute($dto);

        return new AuthResource($authResponse);
    }
}
