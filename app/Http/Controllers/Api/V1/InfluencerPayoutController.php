<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Application\UseCases\Payouts\ListInfluencerPayoutsUseCase;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Influencer Payouts", description="Influencer payouts endpoints")
 */
class InfluencerPayoutController extends Controller
{
    private function user(): User
    {
        /** @var User */
        return Auth::user();
    }

    /**
     * @OA\Get(
     *     path="/api/v1/influencer/payouts",
     *     tags={"Influencer Payouts"},
     *     summary="List influencer payouts (influencer only)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Payouts list"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function index(ListInfluencerPayoutsUseCase $useCase): JsonResponse
    {
        Gate::authorize('influencer-only');

        $data = $useCase->execute($this->user());

        return response()->json(['data' => $data]);
    }
}