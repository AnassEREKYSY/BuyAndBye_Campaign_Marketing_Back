<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\CompleteProfileRequest;
use App\Http\Requests\Users\UpdateProfileRequest;
use Src\Application\Shared\DTOs\ApiResponse;
use Src\Application\Users\DTOs\CompleteProfileRequest as CompleteProfileDTO;
use Src\Application\Users\DTOs\UpdateProfileRequest as UpdateProfileDTO;
use Src\Application\Users\UseCases\CompleteProfileUseCase;
use Src\Application\Users\UseCases\GetProfileStatusUseCase;
use Src\Application\Users\UseCases\GetUserProfileUseCase;
use Src\Application\Users\UseCases\SkipProfileUseCase;
use Src\Application\Users\UseCases\UpdateProfileUseCase;
use Src\Infrastructure\Services\Base64ImageConverter;

class UserProfileController extends Controller
{
    public function getProfile(GetUserProfileUseCase $useCase)
    {
        $result = $useCase->execute();

        return response()->json(new ApiResponse(true, 'Profile retrieved', $result));
    }

    public function status(GetProfileStatusUseCase $useCase)
    {
        $result = $useCase->execute();

        return response()->json(new ApiResponse(true, 'Profile status', $result));
    }

    public function complete(
        CompleteProfileRequest $request,
        CompleteProfileUseCase $useCase,
        Base64ImageConverter $converter
    ) {
        $photoUrl = $request->file('photo') ? $converter->toDataUri($request->file('photo')) : null;
        $bannerUrl = $request->file('store_banner') ? $converter->toDataUri($request->file('store_banner')) : null;
        $dto = new CompleteProfileDTO(
            displayName: $request->display_name,
            phoneNumber: $request->phone_number,
            birthDate: $request->birth_date,
            gender: $request->gender,
            countryCode: $request->country_code,
            locale: $request->locale,
            photoUrl: $photoUrl,
            buyerCategories: $request->buyer_categories,
            buyerInterests: $request->buyer_interests,
            paymentMethods: $request->payment_methods,
            storeName: $request->store_name,
            companyName: $request->company_name,
            vatNumber: $request->vat_number,
            supportEmail: $request->support_email,
            supportPhone: $request->support_phone,
            categoryTags: $request->category_tags,
            storeDescription: $request->store_description,
            storeBannerUrl: $bannerUrl
        );
        $useCase->execute($dto);

        return response()->json(new ApiResponse(true, 'Profile completed'));
    }

    public function skip(SkipProfileUseCase $useCase)
    {
        $useCase->execute();

        return response()->json(new ApiResponse(true, 'Profile skipped'));
    }

    public function update(
        UpdateProfileRequest $request,
        UpdateProfileUseCase $useCase,
        Base64ImageConverter $converter
    ) {
        $photoUrl = $request->file('photo') ? $converter->toDataUri($request->file('photo')) : null;
        $bannerUrl = $request->file('store_banner') ? $converter->toDataUri($request->file('store_banner')) : null;
        $dto = new UpdateProfileDTO(
            displayName: $request->display_name,
            phoneNumber: $request->phone_number,
            birthDate: $request->birth_date,
            gender: $request->gender,
            countryCode: $request->country_code,
            locale: $request->locale,
            photoUrl: $photoUrl,
            buyerCategories: $request->buyer_categories,
            buyerInterests: $request->buyer_interests,
            paymentMethods: $request->payment_methods,
            storeName: $request->store_name,
            companyName: $request->company_name,
            vatNumber: $request->vat_number,
            supportEmail: $request->support_email,
            supportPhone: $request->support_phone,
            categoryTags: $request->category_tags,
            storeDescription: $request->store_description,
            storeBannerUrl: $bannerUrl
        );
        $useCase->execute($dto);

        return response()->json(new ApiResponse(true, 'Profile updated'));
    }
}
