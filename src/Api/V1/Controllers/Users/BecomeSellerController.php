<?php

declare(strict_types=1);

namespace Src\Api\V1\Controllers\Users;

use Src\Api\V1\Controllers\Controller;
use Src\Api\V1\Requests\Users\BecomeSellerRequest;
use Illuminate\Http\JsonResponse;
use Src\Application\Users\DTOs\BecomeSellerRequest as BecomeSellerDTO;
use Src\Application\Users\UseCases\BecomeSellerUseCase;

/**
 * @OA\Post(
 *     path="/api/v1/users/become-seller",
 *     tags={"Users"},
 *     summary="Become a seller",
 *     description="Allows an authenticated buyer to transition to seller role. Status resets to Incomplete requiring profile completion.",
 *     security={{"bearerAuth":{}}},
 *
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"store_name", "country_code"},
 *             @OA\Property(property="store_name", type="string", example="My Awesome Store", description="The name of the seller's store"),
 *             @OA\Property(property="country_code", type="string", example="FR", description="ISO country code where the seller operates")
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Successfully transitioned to seller",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Seller account activated")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid role transition (user is not a buyer)"
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized"
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error"
 *     )
 * )
 */

class BecomeSellerController extends Controller
{
    public function __invoke(
        BecomeSellerRequest $request,
        BecomeSellerUseCase $useCase
    ): JsonResponse {
        $dto = new BecomeSellerDTO(
            storeName: $request->validated('store_name'),
            countryCode: $request->validated('country_code'),
        );

        $useCase->execute($dto);

        return response()->json([
            'success' => true,
            'message' => 'Seller account activated'
        ]);
    }
}
