<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\CompleteProfileRequest;
use Illuminate\Http\JsonResponse;
use Src\Application\Users\DTOs\CompleteProfileRequest as CompleteProfileDTO;
use Src\Application\Users\UseCases\CompleteProfileUseCase;

class CompleteProfileController extends Controller
{
    public function __invoke(
        CompleteProfileRequest $request,
        CompleteProfileUseCase $useCase
    ): JsonResponse {
        $dto = new CompleteProfileDTO(
            displayName: $request->validated('display_name'),
            phoneNumber: $request->validated('phone_number'),
            birthDate: $request->validated('birth_date'),
            gender: $request->validated('gender'),
            countryCode: $request->validated('country_code'),
            locale: $request->validated('locale'),
            photoUrl: $request->validated('photo_url'),
            buyerCategories: $request->validated('buyer_categories'),
            buyerInterests: $request->validated('buyer_interests'),
            paymentMethods: $request->validated('payment_methods'),
            storeName: $request->validated('store_name'),
            companyName: $request->validated('company_name'),
            vatNumber: $request->validated('vat_number'),
            supportEmail: $request->validated('support_email'),
            supportPhone: $request->validated('support_phone'),
            categoryTags: $request->validated('category_tags'),
            storeDescription: $request->validated('store_description'),
            storeBannerUrl: $request->validated('store_banner_url'),
        );

        $useCase->execute($dto);

        return response()->json(['message' => 'Profile completed successfully']);
    }
}
