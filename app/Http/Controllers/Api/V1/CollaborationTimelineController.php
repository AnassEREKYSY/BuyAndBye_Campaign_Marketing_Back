<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Application\UseCases\Analytics\CollaborationTimelineUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\TimelineRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Collaboration Timeline", description="Collaboration timeline analytics")
 */
class CollaborationTimelineController extends Controller
{
    private function user(): User
    {
        /** @var User */
        return Auth::user();
    }

    /**
     * @OA\Get(
     *     path="/api/v1/collaborations/{id}/timeline",
     *     tags={"Collaboration Timeline"},
     *     summary="Collaboration timeline (clicks total + unique)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string", format="uuid")),
     *     @OA\Parameter(name="from", in="query", required=true, @OA\Schema(type="string", format="date")),
     *     @OA\Parameter(name="to", in="query", required=true, @OA\Schema(type="string", format="date")),
     *     @OA\Parameter(name="group", in="query", required=false, @OA\Schema(type="string", enum={"day"})),
     *     @OA\Response(response=200, description="Timeline returned"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function timeline(string $id, TimelineRequest $request, CollaborationTimelineUseCase $useCase): JsonResponse
    {
        $data = $useCase->execute($this->user(), $id, $request->from(), $request->to(), $request->group());

        return response()->json(['data' => $data]);
    }
}