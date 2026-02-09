<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Enums\AccountStatus;
use App\Enums\UserRole;
use App\Exceptions\InvalidCredentialsException;
use App\Exceptions\UserAlreadyExistsException;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\AuthResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"email","password","display_name"},
     *
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="password", type="string", format="password"),
     *             @OA\Property(property="display_name", type="string"),
     *             @OA\Property(property="photo_url", type="string", nullable=true)
     *         )
     *     ),
     *
     *     @OA\Response(response=201, description="User registered successfully"),
     *     @OA\Response(response=409, description="User already exists"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $validated = $request->validated();

        if (User::where('email', $validated['email'])->exists()) {
            throw UserAlreadyExistsException::forEmail($validated['email']);
        }

        $user = User::create([
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'display_name' => $validated['display_name'],
            'photo_url' => $validated['photo_url'] ?? null,
            'role' => UserRole::Buyer->value,
            'status' => AccountStatus::Incomplete->value,
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        return AuthResource::fromUser($user, $token)->response()->setStatusCode(201);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/login",
     *     tags={"Auth"},
     *     summary="Login with email and password",
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"email","password"},
     *
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="password", type="string", format="password")
     *         )
     *     ),
     *
     *     @OA\Response(response=200, description="Authenticated successfully"),
     *     @OA\Response(response=401, description="Invalid credentials"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function login(LoginRequest $request): AuthResource
    {
        $validated = $request->validated();

        $user = User::where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            throw InvalidCredentialsException::create();
        }

        $user->update(['last_login_at' => now()]);

        $token = $user->createToken('api-token')->plainTextToken;

        return AuthResource::fromUser($user, $token);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/auth/me",
     *     tags={"Auth"},
     *     summary="Get authenticated user",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Response(response=200, description="Authenticated user returned"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function me(): UserResource
    {
        /** @var User $user */
        $user = Auth::user();

        return new UserResource($user);
    }
}
