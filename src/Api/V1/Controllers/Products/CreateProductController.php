<?php

declare(strict_types=1);

namespace Src\Api\V1\Controllers\Products;

use Src\Api\V1\Controllers\Controller;
use Src\Api\V1\Requests\Products\CreateProductRequest;
use Src\Api\V1\Resources\Products\ProductResource;
use Illuminate\Support\Facades\Gate;
use Src\Application\Products\DTOs\CreateProductRequest as CreateProductDTO;
use Src\Application\Products\UseCases\CreateProductUseCase;

/**
 * @OA\Post(
 *     path="/api/v1/products/create",
 *     tags={"Products"},
 *     summary="Create a new product",
 *     description="Create a new product (seller or admin only)",
 *     security={{"bearerAuth":{}}},
 *
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"title","description","category_id","condition","price","stock_quantity"},
 *             @OA\Property(property="title", type="string", example="iPhone 15 Pro"),
 *             @OA\Property(property="description", type="string", example="Brand new iPhone"),
 *             @OA\Property(property="category_id", type="string", example="electronics"),
 *             @OA\Property(property="condition", type="string", example="new"),
 *             @OA\Property(property="price", type="number", format="float", example=1299.99),
 *             @OA\Property(property="stock_quantity", type="integer", example=10),
 *             @OA\Property(property="images_data", type="array", @OA\Items(type="string")),
 *             @OA\Property(property="tags", type="array", @OA\Items(type="string")),
 *             @OA\Property(property="weight_kg", type="number", example=0.45),
 *             @OA\Property(property="sku", type="string", example="IP15-PRO-001"),
 *             @OA\Property(property="is_digital", type="boolean", example=false),
 *             @OA\Property(property="allow_returns", type="boolean", example=true),
 *             @OA\Property(property="return_days", type="integer", example=14)
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=201,
 *         description="Product created successfully"
 *     ),
 *     @OA\Response(response=401, description="Unauthorized"),
 *     @OA\Response(response=403, description="Forbidden")
 * )
 */


class CreateProductController extends Controller
{
    public function __invoke(
        CreateProductRequest $request,
        CreateProductUseCase $useCase
    ): ProductResource {
        Gate::authorize('seller-or-admin');

        $dto = new CreateProductDTO(
            title: $request->validated('title'),
            description: $request->validated('description'),
            categoryId: $request->validated('category_id'),
            condition: $request->validated('condition'),
            price: (float) $request->validated('price'),
            stockQuantity: (int) $request->validated('stock_quantity'),
            images: $request->validated('images_data'),
            tags: $request->validated('tags'),
            weightKg: $request->validated('weight_kg') !== null ? (float) $request->validated('weight_kg') : null,
            sku: $request->validated('sku'),
            isDigital: (bool) $request->validated('is_digital'),
            allowReturns: (bool) $request->validated('allow_returns'),
            returnDays: (int) $request->validated('return_days')
        );

        $product = $useCase->execute($dto);

        return new ProductResource($product);
    }
}
