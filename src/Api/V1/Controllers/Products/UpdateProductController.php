<?php

declare(strict_types=1);

namespace Src\Api\V1\Controllers\Products;

use Src\Api\V1\Controllers\Controller;
use Src\Api\V1\Requests\Products\UpdateProductRequest;
use Src\Api\V1\Resources\Products\ProductResource;
use Src\Application\Products\DTOs\UpdateProductRequest as UpdateProductDTO;
use Src\Application\Products\UseCases\GetProductByIdUseCase;
use Src\Application\Products\UseCases\UpdateProductUseCase;

/**
 * @OA\Put(
 *     path="/api/v1/products/update/{productId}",
 *     tags={"Products"},
 *     summary="Update a product",
 *     description="Update an existing product",
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
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="title", type="string"),
 *             @OA\Property(property="description", type="string"),
 *             @OA\Property(property="category_id", type="string"),
 *             @OA\Property(property="condition", type="string"),
 *             @OA\Property(property="price", type="number", format="float"),
 *             @OA\Property(property="stock_quantity", type="integer"),
 *             @OA\Property(property="images_data", type="array", @OA\Items(type="string")),
 *             @OA\Property(property="tags", type="array", @OA\Items(type="string")),
 *             @OA\Property(property="weight_kg", type="number"),
 *             @OA\Property(property="sku", type="string"),
 *             @OA\Property(property="is_digital", type="boolean"),
 *             @OA\Property(property="allow_returns", type="boolean"),
 *             @OA\Property(property="return_days", type="integer")
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Product updated successfully"
 *     ),
 *     @OA\Response(response=401, description="Unauthorized"),
 *     @OA\Response(response=403, description="Forbidden"),
 *     @OA\Response(response=404, description="Product not found")
 * )
 */

class UpdateProductController extends Controller
{
    public function __invoke(
        string $productId,
        UpdateProductRequest $request,
        GetProductByIdUseCase $getById,
        UpdateProductUseCase $useCase
    ): ProductResource {
        $product = $getById->execute($productId);
        $this->authorize('update', $product);

        $dto = new UpdateProductDTO(
            title: $request->validated('title'),
            description: $request->validated('description'),
            categoryId: $request->validated('category_id'),
            condition: $request->validated('condition'),
            price: $request->validated('price') !== null ? (float) $request->validated('price') : null,
            stockQuantity: $request->validated('stock_quantity') !== null ? (int) $request->validated('stock_quantity') : null,
            images: $request->validated('images_data'),
            tags: $request->validated('tags'),
            weightKg: $request->validated('weight_kg') !== null ? (float) $request->validated('weight_kg') : null,
            sku: $request->validated('sku'),
            isDigital: $request->validated('is_digital'),
            allowReturns: $request->validated('allow_returns'),
            returnDays: $request->validated('return_days') !== null ? (int) $request->validated('return_days') : null
        );

        $updated = $useCase->execute($productId, $dto);

        return new ProductResource($updated);
    }
}
