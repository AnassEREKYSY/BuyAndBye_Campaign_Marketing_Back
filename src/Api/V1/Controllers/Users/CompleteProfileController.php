<?php

declare(strict_types=1);

namespace Src\Api\V1\Controllers\Users;

use Src\Api\V1\Controllers\Controller;
use Src\Api\V1\Requests\Users\CompleteProfileRequest;
use Illuminate\Http\JsonResponse;
use Src\Application\Users\DTOs\CompleteProfileRequest as CompleteProfileDTO;
use Src\Application\Users\UseCases\CompleteProfileUseCase;

/**
 * @OA\Post(
 *     path="/api/v1/users/profile/complete",
 *     tags={"Users"},
 *     summary="Complete user profile",
 *     description="Complete buyer or seller profile information",
 *     security={{"bearerAuth":{}}},
 *
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="display_name", type="string", example="John Doe"),
 *             @OA\Property(property="phone_number", type="string", example="+33612345678"),
 *             @OA\Property(property="birth_date", type="string", format="date", example="1995-08-15"),
 *             @OA\Property(property="gender", type="string", example="male"),
 *             @OA\Property(property="country_code", type="string", example="FR"),
 *             @OA\Property(property="locale", type="string", example="fr-FR"),
 *             @OA\Property(property="photo_url", type="string", example="https://cdn.app/avatar.jpg"),
 *             @OA\Property(property="buyer_categories", type="array", @OA\Items(type="string")),
 *             @OA\Property(property="buyer_interests", type="array", @OA\Items(type="string")),
 *             @OA\Property(property="payment_methods", type="array", @OA\Items(type="string")),
 *             @OA\Property(property="store_name", type="string", example="My Store"),
 *             @OA\Property(property="company_name", type="string", example="My Company"),
 *             @OA\Property(property="vat_number", type="string", example="FR123456789"),
 *             @OA\Property(property="support_email", type="string", example="support@store.com"),
 *             @OA\Property(property="support_phone", type="string", example="+33123456789"),
 *             @OA\Property(property="category_tags", type="array", @OA\Items(type="string")),
 *             @OA\Property(property="store_description", type="string"),
 *             @OA\Property(property="store_banner_url", type="string", example="https://cdn.app/banner.jpg")
 *         )
 *     ),
 *
 *     @OA\Response(response=200, description="Profile completed successfully"),
 *     @OA\Response(response=401, description="Unauthorized"),
 *     @OA\Response(response=422, description="Validation error")
 * )
 */


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
