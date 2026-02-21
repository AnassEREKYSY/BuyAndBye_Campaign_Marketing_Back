<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Application\UseCases\Payouts\ApprovePayoutUseCase;
use App\Application\UseCases\Payouts\MarkPayoutPaidUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Admin Payouts", description="Admin payout approval endpoints")
 */
class AdminPayoutController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/v1/admin/payouts/{id}/approve",
     *     tags={"Admin Payouts"},
     *     summary="Approve payout (admin only)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string", format="uuid")),
     *     @OA\Response(response=200, description="Approved"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function approve(string $id, ApprovePayoutUseCase $useCase): JsonResponse
    {
        Gate::authorize('admin-only');

        $payout = $useCase->execute($id);

        return response()->json(['data' => $payout]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/admin/payouts/{id}/mark-paid",
     *     tags={"Admin Payouts"},
     *     summary="Mark payout as paid (admin only)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string", format="uuid")),
     *     @OA\Response(response=200, description="Paid"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function markPaid(string $id, MarkPayoutPaidUseCase $useCase): JsonResponse
    {
        Gate::authorize('admin-only');

        $payout = $useCase->execute($id);

        return response()->json(['data' => $payout]);
    }
}