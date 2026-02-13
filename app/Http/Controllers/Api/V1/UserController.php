<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Application\UseCases\Profile\BecomeSellerUseCase;
use App\Application\UseCases\Profile\CompleteProfileUseCase;
use App\Application\UseCases\Profile\SkipProfileUseCase;
use App\Application\UseCases\Profile\UpdateSellerProfileUseCase;
use App\Application\UseCases\Profile\UpdateUserProfileUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\BecomeSellerRequest;
use App\Http\Requests\CompleteProfileRequest;
use App\Http\Requests\UpdateSellerProfileRequest;
use App\Http\Requests\UpdateUserProfileRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Users",
 *     description="User profile & seller profile management"
 * )
 */
class UserController extends Controller
{
    private function user(): User
    {
        /** @var User */
        return Auth::user();
    }

    /**
     * @OA\Get(
     *     path="/api/v1/users/profile",
     *     tags={"Users"},
     *     summary="Get current authenticated user profile",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="User profile retrieved"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function show(): UserResource
    {
        return new UserResource(
            $this->user()->load(['profile', 'sellerProfile'])
        );
    }

    /**
     * @OA\Get(
     *     path="/api/v1/users/profile/status",
     *     tags={"Users"},
     *     summary="Get profile completion status",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Profile status retrieved"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function status(): UserResource
    {
        return new UserResource(
            $this->user()->load(['profile', 'sellerProfile'])
        );
    }

    /**
     * @OA\Post(
     *     path="/api/v1/users/profile/complete",
     *     tags={"Users"},
     *     summary="Complete minimal profile information",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"display_name"},
     *             @OA\Property(property="display_name", type="string"),
     *             @OA\Property(property="photo_url", type="string", nullable=true)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Profile completed")
     * )
     */
    public function complete(
        CompleteProfileRequest $request,
        CompleteProfileUseCase $useCase
    ): UserResource {
        $user = $this->user();

        $useCase->execute($user, $request->toDto());

        return new UserResource($user->fresh(['profile', 'sellerProfile']));
    }

    /**
     * @OA\Put(
     *     path="/api/v1/users/profile",
     *     tags={"Users"},
     *     summary="Update user profile (buyer side)",
     *     description="Accepts multipart/form-data for avatar upload",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="display_name", type="string"),
     *                 @OA\Property(property="photo", type="string", format="binary"),
     *                 @OA\Property(property="phone_number", type="string"),
     *                 @OA\Property(property="birth_date", type="string", format="date"),
     *                 @OA\Property(property="gender", type="string"),
     *                 @OA\Property(property="country_code", type="string"),
     *                 @OA\Property(property="locale", type="string"),
     *                 @OA\Property(property="buyer_categories", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="buyer_interests", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="payment_methods", type="array", @OA\Items(type="string"))
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="User profile updated successfully"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function updateUserProfile(
        UpdateUserProfileRequest $request,
        UpdateUserProfileUseCase $useCase
    ): UserResource {
        $user = $this->user();

        $useCase->execute($user, $request->toDto());

        return new UserResource($user->fresh(['profile', 'sellerProfile']));
    }

    /**
     * @OA\Put(
     *     path="/api/v1/users/seller-profile",
     *     tags={"Users"},
     *     summary="Update seller profile",
     *     description="Accepts multipart/form-data for store banner upload",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="store_name", type="string"),
     *                 @OA\Property(property="company_name", type="string"),
     *                 @OA\Property(property="vat_number", type="string"),
     *                 @OA\Property(property="support_email", type="string"),
     *                 @OA\Property(property="support_phone", type="string"),
     *                 @OA\Property(property="category_tags", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="store_description", type="string"),
     *                 @OA\Property(property="store_banner", type="string", format="binary")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Seller profile updated successfully")
     * )
     */
    public function updateSellerProfile(
        UpdateSellerProfileRequest $request,
        UpdateSellerProfileUseCase $useCase
    ): UserResource {
        Gate::authorize('seller-or-admin');

        $user = $this->user();

        $useCase->execute($user, $request->toDto());

        return new UserResource($user->fresh(['profile', 'sellerProfile']));
    }

    /**
     * @OA\Post(
     *     path="/api/v1/users/become-seller",
     *     tags={"Users"},
     *     summary="Convert user to seller",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"store_name","country_code"},
     *             @OA\Property(property="store_name", type="string"),
     *             @OA\Property(property="country_code", type="string")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Seller activated")
     * )
     */
    public function becomeSeller(
        BecomeSellerRequest $request,
        BecomeSellerUseCase $useCase
    ): UserResource {
        $user = $this->user();

        $useCase->execute($user, $request->toDto());

        return new UserResource($user->fresh(['profile', 'sellerProfile']));
    }

    /**
     * @OA\Post(
     *     path="/api/v1/users/profile/skip",
     *     tags={"Users"},
     *     summary="Skip profile completion",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Profile skipped")
     * )
     */
    public function skipProfile(SkipProfileUseCase $useCase): UserResource
    {
        $user = $this->user();

        $useCase->execute($user);

        return new UserResource($user->fresh(['profile', 'sellerProfile']));
    }
}
