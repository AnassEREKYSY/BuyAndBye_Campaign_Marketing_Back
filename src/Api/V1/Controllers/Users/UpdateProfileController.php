<?php

declare(strict_types=1);

namespace Src\Api\V1\Controllers\Users;

use Src\Api\V1\Controllers\Controller;
use Src\Api\V1\Requests\Users\UpdateProfileRequest;
use Illuminate\Http\JsonResponse;
use Src\Application\Users\DTOs\UpdateProfileRequest as UpdateProfileDTO;
use Src\Application\Users\UseCases\UpdateProfileUseCase;

/**
 * @OA\Put(
 *     path="/api/v1/users/profile",
 *     tags={"Users"},
 *     summary="Update user profile",
 *     description="Update user profile fields",
 *     security={{"bearerAuth":{}}},
 *
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="display_name", type="string"),
 *             @OA\Property(property="phone_number", type="string"),
 *             @OA\Property(property="birth_date", type="string", format="date"),
 *             @OA\Property(property="gender", type="string"),
 *             @OA\Property(property="country_code", type="string"),
 *             @OA\Property(property="locale", type="string"),
 *             @OA\Property(property="photo_url", type="string"),
 *             @OA\Property(property="buyer_categories", type="array", @OA\Items(type="string")),
 *             @OA\Property(property="buyer_interests", type="array", @OA\Items(type="string")),
 *             @OA\Property(property="payment_methods", type="array", @OA\Items(type="string")),
 *             @OA\Property(property="store_name", type="string"),
 *             @OA\Property(property="company_name", type="string"),
 *             @OA\Property(property="vat_number", type="string"),
 *             @OA\Property(property="support_email", type="string"),
 *             @OA\Property(property="support_phone", type="string"),
 *             @OA\Property(property="category_tags", type="array", @OA\Items(type="string")),
 *             @OA\Property(property="store_description", type="string"),
 *             @OA\Property(property="store_banner_url", type="string")
 *         )
 *     ),
 *
 *     @OA\Response(response=200, description="Profile updated successfully"),
 *     @OA\Response(response=401, description="Unauthorized"),
 *     @OA\Response(response=422, description="Validation error")
 * )
 */

class UpdateProfileController extends Controller
{
    public function __invoke(
        UpdateProfileRequest $request,
        UpdateProfileUseCase $useCase
    ): JsonResponse {
        $validatedData = $request->validated();

        $dto = new UpdateProfileDTO(
            displayName: $validatedData['display_name'] ?? null,
            phoneNumber: $validatedData['phone_number'] ?? null,
            birthDate: $validatedData['birth_date'] ?? null,
            gender: $validatedData['gender'] ?? null,
            countryCode: $validatedData['country_code'] ?? null,
            locale: $validatedData['locale'] ?? null,
            photoUrl: $validatedData['photo_url'] ?? null,
            buyerCategories: $validatedData['buyer_categories'] ?? null,
            buyerInterests: $validatedData['buyer_interests'] ?? null,
            paymentMethods: $validatedData['payment_methods'] ?? null,
            storeName: $validatedData['store_name'] ?? null,
            companyName: $validatedData['company_name'] ?? null,
            vatNumber: $validatedData['vat_number'] ?? null,
            supportEmail: $validatedData['support_email'] ?? null,
            supportPhone: $validatedData['support_phone'] ?? null,
            categoryTags: $validatedData['category_tags'] ?? null,
            storeDescription: $validatedData['store_description'] ?? null,
            storeBannerUrl: $validatedData['store_banner_url'] ?? null,
        );

        $useCase->execute($dto);

        return response()->json(['message' => 'Profile updated successfully']);
    }
}
