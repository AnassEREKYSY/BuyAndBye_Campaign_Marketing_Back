<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Application\UseCases\Auth\LoginUserUseCase;
use App\Application\UseCases\Auth\RegisterUserUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\AuthResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Auth", description="Authentication endpoints")
 */
class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/v1/auth/register",
     *     tags={"Auth"},
     *     summary="Register a new user",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"email","password","display_name"},
     *                 @OA\Property(property="email", type="string", format="email"),
     *                 @OA\Property(property="password", type="string"),
     *                 @OA\Property(property="display_name", type="string"),
     *                 @OA\Property(property="photo", type="string", format="binary")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=201, description="User registered successfully"),
     *     @OA\Response(response=409, description="User already exists"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function register(
        RegisterRequest $request,
        RegisterUserUseCase $useCase
    ): JsonResponse {
        [$user, $token] = $useCase->execute($request->toDto());

        return AuthResource::fromUser($user, $token)
            ->response()
            ->setStatusCode(201);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/login",
     *     tags={"Auth"},
     *     summary="Login with email and password",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="password", type="string", format="password")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Authenticated successfully"),
     *     @OA\Response(response=401, description="Invalid credentials"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function login(
        LoginRequest $request,
        LoginUserUseCase $useCase
    ): AuthResource {
        [$user, $token] = $useCase->execute($request->toDto());

        return AuthResource::fromUser($user, $token);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/auth/me",
     *     tags={"Auth"},
     *     summary="Get authenticated user",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Authenticated user returned"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function me(): UserResource
    {
        /** @var User $user */
        $user = Auth::user();

        $user->load(['profile', 'sellerProfile']);

        return new UserResource($user);
    }
}
