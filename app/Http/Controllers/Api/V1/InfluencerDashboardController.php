<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Application\UseCases\Analytics\InfluencerDashboardUseCase;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Influencer Dashboard", description="Influencer analytics endpoints")
 */
class InfluencerDashboardController extends Controller
{
    private function user(): User
    {
        /** @var User */
        return Auth::user();
    }

    /**
     * @OA\Get(
     *     path="/api/v1/influencer/dashboard",
     *     tags={"Influencer Dashboard"},
     *     summary="Get influencer dashboard (influencer only)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Dashboard returned"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function dashboard(InfluencerDashboardUseCase $useCase): JsonResponse
    {
        Gate::authorize('influencer-only');

        $data = $useCase->execute($this->user());

        return response()->json(['data' => $data]);
    }
}