<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Application\UseCases\Profile\UpdateBrandProfileUseCase;
use App\Application\UseCases\Profile\UpdateInfluencerProfileUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateBrandProfileRequest;
use App\Http\Requests\UpdateInfluencerProfileRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @OA\Tag(name="Users", description="Current user profile management")
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
            $this->user()->load(['brandProfile', 'influencerProfile'])
        );
    }

    /**
     * @OA\Put(
     *     path="/api/v1/users/profile/brand",
     *     tags={"Users"},
     *     summary="Update brand profile",
     *     description="Only for users with role=brand. Accepts multipart/form-data for logo upload.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="brand_name", type="string", example="My Brand"),
     *                 @OA\Property(property="website_url", type="string", format="uri", example="https://brand.com"),
     *                 @OA\Property(property="industry", type="string", example="Beauty"),
     *                 @OA\Property(property="contact_email", type="string", format="email", example="contact@brand.com"),
     *                 @OA\Property(property="contact_phone", type="string", example="+212600000000"),
     *                 @OA\Property(property="description", type="string", example="We sell amazing products"),
     *                 @OA\Property(property="logo", type="string", format="binary")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Brand profile updated"),
     *     @OA\Response(response=400, description="Wrong role"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function updateBrandProfile(UpdateBrandProfileRequest $request, UpdateBrandProfileUseCase $useCase): UserResource
    {
        $user = $this->user();

        if (! $user->role->isBrand()) {
            throw new BadRequestHttpException('Only brand users can update brand profile.');
        }

        $useCase->execute($user, $request->toDto());

        return new UserResource($user->fresh(['brandProfile', 'influencerProfile']));
    }

    /**
     * @OA\Put(
     *     path="/api/v1/users/profile/influencer",
     *     tags={"Users"},
     *     summary="Update influencer profile",
     *     description="Only for users with role=influencer.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="niche", type="string", example="Tech"),
     *             @OA\Property(property="instagram_url", type="string", format="uri", example="https://instagram.com/username"),
     *             @OA\Property(property="tiktok_url", type="string", format="uri", example="https://tiktok.com/@username"),
     *             @OA\Property(property="youtube_url", type="string", format="uri", example="https://youtube.com/@username"),
     *             @OA\Property(property="followers_instagram", type="integer", example=50000),
     *             @OA\Property(property="followers_tiktok", type="integer", example=120000),
     *             @OA\Property(property="followers_youtube", type="integer", example=10000),
     *             @OA\Property(property="avg_engagement_rate", type="number", format="float", example=4.2),
     *             @OA\Property(property="country_code", type="string", example="MA"),
     *             @OA\Property(property="language", type="string", example="fr"),
     *             @OA\Property(property="media_kit_url", type="string", format="uri", example="https://drive.google.com/...")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Influencer profile updated"),
     *     @OA\Response(response=400, description="Wrong role"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function updateInfluencerProfile(UpdateInfluencerProfileRequest $request, UpdateInfluencerProfileUseCase $useCase): UserResource
    {
        $user = $this->user();

        if (! $user->role->isInfluencer()) {
            throw new BadRequestHttpException('Only influencer users can update influencer profile.');
        }

        $useCase->execute($user, $request->toDto());

        return new UserResource($user->fresh(['brandProfile', 'influencerProfile']));
    }
}