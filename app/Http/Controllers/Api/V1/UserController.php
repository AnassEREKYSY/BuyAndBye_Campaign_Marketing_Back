<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Application\DTOs\Profile\BecomeSellerDTO;
use App\Application\DTOs\Profile\CompleteProfileDTO;
use App\Application\DTOs\Profile\UpdateProfileDTO;
use App\Application\UseCases\Profile\BecomeSellerUseCase;
use App\Application\UseCases\Profile\CompleteProfileUseCase;
use App\Application\UseCases\Profile\SkipProfileUseCase;
use App\Application\UseCases\Profile\UpdateProfileUseCase;
use App\Enums\AccountStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\BecomeSellerRequest;
use App\Http\Requests\CompleteProfileRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Users", description="User profile endpoints")
 */
class UserController extends Controller
{
    private function user(): User
    {
        return Auth::user();
    }

    /**
     * @OA\Get(
     *     path="/api/v1/profile",
     *     tags={"Users"},
     *     summary="Get user profile",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Profile retrieved successfully"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function show(): UserResource
    {
        return new UserResource($this->user());
    }

    /**
     * @OA\Get(
     *     path="/api/v1/profile/status",
     *     tags={"Users"},
     *     summary="Get profile completion status",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Profile status retrieved"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function status(): JsonResponse
    {
        $user = $this->user();

        return response()->json([
            'status' => $user->status->value,
            'isProfileComplete' => $user->status === AccountStatus::Active,
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/profile/complete",
     *     tags={"Users"},
     *     summary="Complete user profile",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="display_name", type="string"),
     *             @OA\Property(property="photo_url", type="string")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Profile completed successfully"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function complete(
        CompleteProfileRequest $request,
        CompleteProfileUseCase $useCase
    ): JsonResponse {
        $dto = new CompleteProfileDTO(
            displayName: $request->display_name,
            photoUrl: $request->photo_url
        );

        $useCase->execute($this->user(), $dto);

        return response()->json(['message' => 'Profile completed successfully']);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/profile",
     *     tags={"Users"},
     *     summary="Update user profile",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="display_name", type="string"),
     *             @OA\Property(property="photo_url", type="string")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Profile updated successfully"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function update(
        UpdateProfileRequest $request,
        UpdateProfileUseCase $useCase
    ): JsonResponse {
        $dto = new UpdateProfileDTO(
            displayName: $request->display_name,
            photoUrl: $request->photo_url
        );

        $useCase->execute($this->user(), $dto);

        return response()->json(['message' => 'Profile updated successfully']);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/profile/skip",
     *     tags={"Users"},
     *     summary="Skip profile completion",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Profile skipped successfully"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function skip(
        SkipProfileUseCase $useCase
    ): JsonResponse {
        $useCase->execute($this->user());

        return response()->json(['message' => 'Profile skipped successfully']);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/become-seller",
     *     tags={"Users"},
     *     summary="Become a seller",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"store_name", "country_code"},
     *             @OA\Property(property="store_name", type="string"),
     *             @OA\Property(property="country_code", type="string")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Seller account activated"),
     *     @OA\Response(response=400, description="Invalid role transition"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function becomeSeller(
        BecomeSellerRequest $request,
        BecomeSellerUseCase $useCase
    ): JsonResponse {
        $dto = new BecomeSellerDTO(
            storeName: $request->store_name,
            countryCode: $request->country_code
        );

        $useCase->execute($this->user(), $dto);

        return response()->json([
            'success' => true,
            'message' => 'Seller account activated',
        ]);
    }
}
