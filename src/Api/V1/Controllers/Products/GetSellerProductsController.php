<?php

declare(strict_types=1);

namespace Src\Api\V1\Controllers\Products;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;
use Src\Api\V1\Controllers\Controller;
use Src\Api\V1\Resources\Products\PagedProductsResource;
use Src\Application\Products\UseCases\GetSellerProductsUseCase;

/**
 * @OA\Get(
 *     path="/api/v1/products/get-mine",
 *     tags={"Products"},
 *     summary="Get seller products",
 *     description="Retrieve paginated list of products for authenticated seller",
 *     security={{"bearerAuth":{}}},
 *
 *     @OA\Parameter(
 *         name="page",
 *         in="query",
 *         description="Page number",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Parameter(
 *         name="pageSize",
 *         in="query",
 *         description="Items per page",
 *         @OA\Schema(type="integer", example=20)
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Products list retrieved successfully"
 *     ),
 *     @OA\Response(response=401, description="Unauthorized"),
 *     @OA\Response(response=403, description="Forbidden")
 * )
 */
class GetSellerProductsController extends Controller
{
    public function __invoke(GetSellerProductsUseCase $useCase): JsonResource
    {
        Gate::authorize('seller-or-admin');

        $page = (int) request()->query('page', 1);
        $pageSize = (int) request()->query('pageSize', 20);
        $paged = $useCase->execute($page, $pageSize);

        return new PagedProductsResource($paged);
    }
}
