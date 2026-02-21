<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Application\UseCases\Collaborations\GetMyCollaborationUseCase;
use App\Application\UseCases\Collaborations\ListMyCollaborationsUseCase;
use App\Http\Controllers\Controller;
use App\Http\Resources\CollaborationResource;
use App\Models\User;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Collaborations", description="Accepted collaborations with tracking and promo codes")
 */
class CollaborationController extends Controller
{
    private function user(): User
    {
        /** @var User */
        return Auth::user();
    }

    /**
     * @OA\Get(
     *     path="/api/v1/collaborations",
     *     tags={"Collaborations"},
     *     summary="List my collaborations (brand sees own, influencer sees own)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer", example=1)),
     *     @OA\Parameter(name="size", in="query", required=false, @OA\Schema(type="integer", example=20)),
     *     @OA\Response(response=200, description="Collaborations list"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function index(ListMyCollaborationsUseCase $useCase): AnonymousResourceCollection
    {
        $page = (int) request()->query('page', 1);
        $size = (int) request()->query('size', 20);

        $items = $useCase->execute($this->user(), $page, $size);

        return CollaborationResource::collection($items);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/collaborations/{id}",
     *     tags={"Collaborations"},
     *     summary="Get a collaboration by id (only if belongs to me)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string", format="uuid")),
     *     @OA\Response(response=200, description="Collaboration returned"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function show(string $id, GetMyCollaborationUseCase $useCase): CollaborationResource
    {
        $collab = $useCase->execute($this->user(), $id);

        return new CollaborationResource($collab);
    }
}