<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Application\UseCases\Payouts\CloseCollaborationPayoutUseCase;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Brand Payouts", description="Brand payout lifecycle endpoints")
 */
class BrandPayoutController extends Controller
{
    private function user(): User
    {
        /** @var User */
        return Auth::user();
    }

    /**
     * @OA\Post(
     *     path="/api/v1/brand/collaborations/{id}/payouts/close",
     *     tags={"Brand Payouts"},
     *     summary="Close payout for a period (brand only)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string", format="uuid")),
     *     @OA\Parameter(name="from", in="query", required=true, @OA\Schema(type="string", format="date", example="2026-02-01")),
     *     @OA\Parameter(name="to", in="query", required=true, @OA\Schema(type="string", format="date", example="2026-02-21")),
     *     @OA\Response(response=200, description="Payout created"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=409, description="Already closed for this period")
     * )
     */
    public function close(string $id, CloseCollaborationPayoutUseCase $useCase): JsonResponse
    {
        Gate::authorize('brand-only');

        $from = request()->query('from');
        $to = request()->query('to');

        $start = $from . ' 00:00:00';
        $end = $to . ' 23:59:59';

        $payout = $useCase->execute($this->user(), $id, $start, $end);

        return response()->json(['data' => $payout]);
    }
}