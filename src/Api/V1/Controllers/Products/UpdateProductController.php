<?php

declare(strict_types=1);

namespace Src\Api\V1\Controllers\Products;

use Src\Api\V1\Controllers\Controller;
use Src\Api\V1\Requests\Products\UpdateProductRequest;
use Src\Api\V1\Resources\Products\ProductResource;
use Src\Application\Products\DTOs\UpdateProductRequest as UpdateProductDTO;
use Src\Application\Products\UseCases\GetProductByIdUseCase;
use Src\Application\Products\UseCases\UpdateProductUseCase;

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
