<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Application\Dtos\Product\CreateProductDTO;
use App\Application\Dtos\Product\UpdateProductDTO;
use App\Application\UseCases\Product\CreateProductUseCase;
use App\Application\UseCases\Product\DeleteProductUseCase;
use App\Application\UseCases\Product\GetSellerProductsUseCase;
use App\Application\UseCases\Product\UpdateProductUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Products", description="Product management endpoints")
 */
class ProductController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/products",
     *     tags={"Products"},
     *     summary="Get seller products",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="page", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="pageSize", in="query", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Products list retrieved"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function index(GetSellerProductsUseCase $useCase): JsonResponse
    {
        Gate::authorize('seller-or-admin');

        $page = (int) request()->query('page', 1);
        $pageSize = (int) request()->query('pageSize', 20);

        $products = $useCase->execute(auth()->id(), $page, $pageSize);

        return response()->json([
            'items' => ProductResource::collection($products->items()),
            'page' => $products->currentPage(),
            'pageSize' => $products->perPage(),
            'total' => $products->total(),
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/products",
     *     tags={"Products"},
     *     summary="Create a new product",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=201, description="Product created")
     * )
     */
    public function store(
        CreateProductRequest $request,
        CreateProductUseCase $useCase
    ): JsonResponse {
        Gate::authorize('seller-or-admin');

        $validated = $request->validated();

        $dto = new CreateProductDTO(
            sellerId: auth()->id(),
            title: $validated['title'],
            description: $validated['description'] ?? null,
            categoryId: $validated['category_id'] ?? null,
            condition: $validated['condition'],
            price: (float) $validated['price'],
            stockQuantity: (int) $validated['stock_quantity'],
            images: $validated['images_data'] ?? null,
            tags: $validated['tags'] ?? null,
            weightKg: $validated['weight_kg'] ?? null,
            sku: $validated['sku'] ?? null,
            isDigital: $validated['is_digital'],
            allowReturns: $validated['allow_returns'],
            returnDays: (int) $validated['return_days'],
        );

        $product = $useCase->execute($dto);

        return (new ProductResource($product))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/products/{product}",
     *     tags={"Products"},
     *     summary="Get product by ID",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Product retrieved")
     * )
     */
    public function show(Product $product): ProductResource
    {
        $this->authorize('view', $product);

        return new ProductResource($product);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/products/{product}",
     *     tags={"Products"},
     *     summary="Update a product",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Product updated")
     * )
     */
    public function update(
        UpdateProductRequest $request,
        Product $product,
        UpdateProductUseCase $useCase
    ): ProductResource {
        $this->authorize('update', $product);

        $validated = $request->validated();

        $dto = new UpdateProductDTO(
            title: $validated['title'] ?? null,
            description: $validated['description'] ?? null,
            categoryId: $validated['category_id'] ?? null,
            condition: $validated['condition'] ?? null,
            price: $validated['price'] ?? null,
            stockQuantity: $validated['stock_quantity'] ?? null,
            images: $validated['images_data'] ?? null,
            tags: $validated['tags'] ?? null,
            weightKg: $validated['weight_kg'] ?? null,
            sku: $validated['sku'] ?? null,
            isDigital: $validated['is_digital'] ?? null,
            allowReturns: $validated['allow_returns'] ?? null,
            returnDays: $validated['return_days'] ?? null,
        );

        $useCase->execute($product, $dto);

        return new ProductResource($product->fresh());
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/products/{product}",
     *     tags={"Products"},
     *     summary="Delete a product",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Product deleted")
     * )
     */
    public function destroy(
        Product $product,
        DeleteProductUseCase $useCase
    ): JsonResponse {
        $this->authorize('delete', $product);

        $useCase->execute($product);

        return response()->json([
            'success' => true,
            'message' => 'Product deleted'
        ]);
    }
}
