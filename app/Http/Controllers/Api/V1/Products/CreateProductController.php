<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Products;

use App\Http\Controllers\Controller;
use App\Http\Requests\Products\CreateProductRequest;
use App\Http\Resources\Products\ProductResource;
use Illuminate\Support\Facades\Gate;
use Src\Application\Products\DTOs\CreateProductRequest as CreateProductDTO;
use Src\Application\Products\UseCases\CreateProductUseCase;

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
