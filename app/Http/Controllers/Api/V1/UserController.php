<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Application\Dtos\Profile\BecomeSellerDTO;
use App\Application\Dtos\Profile\CompleteProfileDTO;
use App\Application\Dtos\Profile\UpdateUserProfileDTO;
use App\Application\Dtos\Profile\UpdateSellerProfileDTO;
use App\Application\UseCases\Profile\BecomeSellerUseCase;
use App\Application\UseCases\Profile\CompleteProfileUseCase;
use App\Application\UseCases\Profile\UpdateUserProfileUseCase;
use App\Application\UseCases\Profile\UpdateSellerProfileUseCase;
use App\Enums\AccountStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\BecomeSellerRequest;
use App\Http\Requests\CompleteProfileRequest;
use App\Http\Requests\UpdateUserProfileRequest;
use App\Http\Requests\UpdateSellerProfileRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
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
        return Auth::user();
    }

    /**
     * @OA\Get(
     *     path="/api/v1/users/profile/get",
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
     *     path="/api/v1/users/profile/status/get",
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
            'is_profile_complete' => $user->status === AccountStatus::Active,
        ]);
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
     *     path="/api/v1/users/profile/update",
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
    ): JsonResponse {
        $dto = new UpdateUserProfileDTO(
            displayName: $request->display_name,
            photo: $request->file('photo'),
            phoneNumber: $request->phone_number,
            birthDate: $request->birth_date,
            gender: $request->gender,
            countryCode: $request->country_code,
            locale: $request->locale,
            buyerCategories: $request->buyer_categories,
            buyerInterests: $request->buyer_interests,
            paymentMethods: $request->payment_methods,
        );

        $useCase->execute($this->user(), $dto);

        return response()->json(['message' => 'User profile updated successfully']);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/users/seller-profile/update",
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
    ): JsonResponse {
        $dto = new UpdateSellerProfileDTO(
            storeName: $request->store_name,
            companyName: $request->company_name,
            vatNumber: $request->vat_number,
            supportEmail: $request->support_email,
            supportPhone: $request->support_phone,
            categoryTags: $request->category_tags,
            storeDescription: $request->store_description,
            storeBanner: $request->file('store_banner'),
        );

        $useCase->execute($this->user(), $dto);

        return response()->json(['message' => 'Seller profile updated successfully']);
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
