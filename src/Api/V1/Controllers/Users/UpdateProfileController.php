<?php

declare(strict_types=1);

namespace Src\Api\V1\Controllers\Users;

use Src\Api\V1\Controllers\Controller;
use Src\Api\V1\Requests\Users\UpdateProfileRequest;
use Illuminate\Http\JsonResponse;
use Src\Application\Users\DTOs\UpdateProfileRequest as UpdateProfileDTO;
use Src\Application\Users\UseCases\UpdateProfileUseCase;

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
