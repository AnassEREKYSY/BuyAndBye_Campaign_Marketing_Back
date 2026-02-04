<?php

declare(strict_types=1);

namespace Src\Api\V1\Controllers\Auth;

use OpenApi\Annotations as OA;
use Src\Api\V1\Controllers\Controller;
use Src\Api\V1\Requests\Auth\RegisterRequest;
use Src\Api\V1\Resources\Auth\AuthResource;
use Src\Application\Auth\DTOs\RegisterRequest as RegisterDTO;
use Src\Application\Auth\UseCases\RegisterUserUseCase;

/**
 * @OA\Post(
 *     path="/api/v1/auth/register",
 *     tags={"Auth"},
 *     summary="Register a new user",
 *     description="Creates a new user account and returns an auth token",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email","password","display_name"},
 *             @OA\Property(property="email", type="string", format="email"),
 *             @OA\Property(property="password", type="string", format="password"),
 *             @OA\Property(property="display_name", type="string"),
 *             @OA\Property(property="photo_url", type="string", nullable=true)
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="User registered successfully"
 *     ),
 *     @OA\Response(
 *         response=409,
 *         description="User already exists"
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error"
 *     )
 * )
 */
final class RegisterController extends Controller
{
    public function __invoke(
        RegisterRequest $request,
        RegisterUserUseCase $useCase
    ): AuthResource {
        $dto = new RegisterDTO(
            email: $request->validated('email'),
            password: $request->validated('password'),
            displayName: $request->validated('display_name'),
            photoUrl: $request->validated('photo_url'),
        );

        $authResponse = $useCase->execute($dto);

        return new AuthResource($authResponse);
    }
}
