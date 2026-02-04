<?php

declare(strict_types=1);

namespace Src\Api\V1\Controllers\Auth;

use OpenApi\Annotations as OA;
use Src\Api\V1\Controllers\Controller;
use Src\Api\V1\Requests\Auth\LoginRequest;
use Src\Api\V1\Resources\Auth\AuthResource;
use Src\Application\Auth\DTOs\LoginRequest as LoginDTO;
use Src\Application\Auth\UseCases\LoginUserUseCase;

/**
 * @OA\Post(
 *     path="/api/v1/auth/login",
 *     tags={"Auth"},
 *     summary="Login with email and password",
 *     description="Authenticates a user using email and password and returns an auth token",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email","password"},
 *             @OA\Property(property="email", type="string", format="email"),
 *             @OA\Property(property="password", type="string", format="password")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Authenticated successfully"
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Invalid credentials"
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error"
 *     )
 * )
 */
final class LoginController extends Controller
{
    public function __invoke(
        LoginRequest $request,
        LoginUserUseCase $useCase
    ): AuthResource {
        $dto = new LoginDTO(
            email: $request->validated('email'),
            password: $request->validated('password'),
        );

        $authResponse = $useCase->execute($dto);

        return new AuthResource($authResponse);
    }
}
