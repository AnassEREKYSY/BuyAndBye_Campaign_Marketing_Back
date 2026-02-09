<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Enums\AccountStatus;
use App\Enums\UserRole;
use App\Exceptions\InvalidRoleTransitionException;
use App\Exceptions\UserAlreadySellerException;
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
        /** @var User $user */
        $user = Auth::user();

        return $user;
    }

    /**
     * @OA\Get(
     *     path="/api/v1/profile",
     *     tags={"Users"},
     *     summary="Get user profile",
     *     security={{"bearerAuth":{}}},
     *
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
     *
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
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="display_name", type="string"),
     *             @OA\Property(property="phone_number", type="string"),
     *             @OA\Property(property="birth_date", type="string", format="date"),
     *             @OA\Property(property="gender", type="string"),
     *             @OA\Property(property="country_code", type="string"),
     *             @OA\Property(property="locale", type="string")
     *         )
     *     ),
     *
     *     @OA\Response(response=200, description="Profile completed successfully"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function complete(CompleteProfileRequest $request): JsonResponse
    {
        $user = $this->user();
        $validated = $request->validated();

        $user->update([
            'display_name' => $validated['display_name'],
            'photo_url' => $validated['photo_url'] ?? $user->photo_url,
            'profile_completed' => true,
            'profile_skipped' => false,
            'status' => AccountStatus::Active->value,
        ]);

        return response()->json(['message' => 'Profile completed successfully']);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/profile",
     *     tags={"Users"},
     *     summary="Update user profile",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="display_name", type="string"),
     *             @OA\Property(property="phone_number", type="string"),
     *             @OA\Property(property="photo_url", type="string")
     *         )
     *     ),
     *
     *     @OA\Response(response=200, description="Profile updated successfully"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = $this->user();
        $validated = $request->validated();

        $updateData = array_filter([
            'display_name' => $validated['display_name'] ?? null,
            'photo_url' => $validated['photo_url'] ?? null,
        ], fn ($value) => $value !== null);

        if (! empty($updateData)) {
            $user->update($updateData);
        }

        return response()->json(['message' => 'Profile updated successfully']);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/profile/skip",
     *     tags={"Users"},
     *     summary="Skip profile completion",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Response(response=200, description="Profile skipped successfully"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function skip(): JsonResponse
    {
        $user = $this->user();

        $user->update([
            'profile_skipped' => true,
            'status' => AccountStatus::Skipped->value,
        ]);

        return response()->json(['message' => 'Profile skipped successfully']);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/become-seller",
     *     tags={"Users"},
     *     summary="Become a seller",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"store_name", "country_code"},
     *
     *             @OA\Property(property="store_name", type="string"),
     *             @OA\Property(property="country_code", type="string")
     *         )
     *     ),
     *
     *     @OA\Response(response=200, description="Seller account activated"),
     *     @OA\Response(response=400, description="Invalid role transition"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function becomeSeller(BecomeSellerRequest $request): JsonResponse
    {
        $user = $this->user();

        if ($user->role === UserRole::Seller) {
            throw UserAlreadySellerException::forUser($user->id);
        }

        if ($user->role !== UserRole::Buyer) {
            throw InvalidRoleTransitionException::fromRole($user->role);
        }

        $user->update([
            'role' => UserRole::Seller->value,
            'status' => AccountStatus::Incomplete->value,
            'profile_completed' => false,
            'profile_skipped' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Seller account activated',
        ]);
    }
}
