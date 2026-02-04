<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Products;

use App\Http\Controllers\Controller;
use App\Http\Requests\Products\CreateProductRequest;
use App\Http\Requests\Products\UpdateProductRequest;
use Illuminate\Support\Facades\Gate;
use Src\Application\Products\DTOs\CreateProductRequest as CreateProductDTO;
use Src\Application\Products\DTOs\UpdateProductRequest as UpdateProductDTO;
use Src\Application\Products\UseCases\CreateProductUseCase;
use Src\Application\Products\UseCases\DeleteProductUseCase;
use Src\Application\Products\UseCases\GetProductByIdUseCase;
use Src\Application\Products\UseCases\GetSellerProductsUseCase;
use Src\Application\Products\UseCases\UpdateProductUseCase;
use Src\Application\Shared\DTOs\ApiResponse;
use Src\Infrastructure\Services\Base64ImageConverter;

class ProductsController extends Controller
{
    public function create(
        CreateProductRequest $request,
        CreateProductUseCase $useCase,
        Base64ImageConverter $converter
    ) {
        Gate::authorize('seller-or-admin');

        $images = $request->file('images')
            ? $converter->toDataUriList($request->file('images'))
            : null;

        $dto = new CreateProductDTO(
            title: $request->title,
            description: $request->description,
            categoryId: $request->category_id,
            condition: $request->condition,
            price: (float) $request->price,
            stockQuantity: (int) $request->stock_quantity,
            images: $images,
            tags: $request->tags,
            weightKg: $request->weight_kg !== null ? (float) $request->weight_kg : null,
            sku: $request->sku,
            isDigital: (bool) $request->is_digital,
            allowReturns: (bool) $request->allow_returns,
            returnDays: (int) $request->return_days
        );

        $product = $useCase->execute($dto);

        return response()->json(new ApiResponse(true, 'Product created', $product));
    }

    public function getMine(GetSellerProductsUseCase $useCase)
    {
        Gate::authorize('seller-or-admin');

        $page = (int) request()->query('page', 1);
        $pageSize = (int) request()->query('pageSize', 20);
        $paged = $useCase->execute($page, $pageSize);

        return response()->json(new ApiResponse(true, 'Products retrieved', $paged));
    }

    public function getById(string $productId, GetProductByIdUseCase $useCase)
    {
        $product = $useCase->execute($productId);
        $this->authorize('view', $product);

        return response()->json(new ApiResponse(true, 'Product retrieved', $product));
    }

    public function update(
        string $productId,
        UpdateProductRequest $request,
        GetProductByIdUseCase $getById,
        UpdateProductUseCase $useCase,
        Base64ImageConverter $converter
    ) {
        $product = $getById->execute($productId);
        $this->authorize('update', $product);

        $images = $request->file('images')
            ? $converter->toDataUriList($request->file('images'))
            : null;

        $dto = new UpdateProductDTO(
            title: $request->title,
            description: $request->description,
            categoryId: $request->category_id,
            condition: $request->condition,
            price: $request->price !== null ? (float) $request->price : null,
            stockQuantity: $request->stock_quantity !== null ? (int) $request->stock_quantity : null,
            images: $images,
            tags: $request->tags,
            weightKg: $request->weight_kg !== null ? (float) $request->weight_kg : null,
            sku: $request->sku,
            isDigital: $request->is_digital,
            allowReturns: $request->allow_returns,
            returnDays: $request->return_days !== null ? (int) $request->return_days : null
        );

        $updated = $useCase->execute($productId, $dto);

        return response()->json(new ApiResponse(true, 'Product updated', $updated));
    }

    public function delete(
        string $productId,
        GetProductByIdUseCase $getById,
        DeleteProductUseCase $useCase
    ) {
        $product = $getById->execute($productId);
        $this->authorize('delete', $product);

        $useCase->execute($productId);

        return response()->json(new ApiResponse(true, 'Product deleted'));
    }
}
