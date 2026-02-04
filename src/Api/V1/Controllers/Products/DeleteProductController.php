<?php

declare(strict_types=1);

namespace Src\Api\V1\Controllers\Products;

use Src\Api\V1\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Src\Application\Products\UseCases\DeleteProductUseCase;
use Src\Application\Products\UseCases\GetProductByIdUseCase;

/**
 * @OA\Delete(
 *     path="/api/v1/products/delete/{productId}",
 *     tags={"Products"},
 *     summary="Delete a product",
 *     description="Delete a product by ID (owner or admin only)",
 *     security={{"bearerAuth":{}}},
 *
 *     @OA\Parameter(
 *         name="productId",
 *         in="path",
 *         required=true,
 *         description="Product ID",
 *         @OA\Schema(type="string")
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Product deleted successfully"
 *     ),
 *     @OA\Response(response=401, description="Unauthorized"),
 *     @OA\Response(response=403, description="Forbidden"),
 *     @OA\Response(response=404, description="Product not found")
 * )
 */

class DeleteProductController extends Controller
{
    public function __invoke(
        string $productId,
        GetProductByIdUseCase $getById,
        DeleteProductUseCase $useCase
    ): JsonResponse {
        $product = $getById->execute($productId);
        $this->authorize('delete', $product);

        $useCase->execute($productId);

        return response()->json(['success' => true, 'message' => 'Product deleted']);
    }
}
