<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

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
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;
use OpenApi\Annotations as OA;
use App\Application\UseCases\Product\UpdateProductStatusUseCase;
use App\Http\Requests\UpdateProductStatusRequest; 
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
    public function index(Request $request, GetSellerProductsUseCase $useCase): AnonymousResourceCollection
    {
        Gate::authorize('seller-or-admin');

        $page = (int) $request->query('page', 1);
        $pageSize = min((int) $request->query('pageSize', 20), 100);

        $products = $useCase->execute(auth()->id(), $page, $pageSize);

        return ProductResource::collection($products);
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

        $product = $useCase->execute($request->toDto());

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

        $useCase->execute($product, $request->toDto());

        return new ProductResource($product->fresh());
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/products/{product}",
     *     tags={"Products"},
     *     summary="Delete a product",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=204, description="Product deleted")
     * )
     */
    public function destroy(
        Product $product,
        DeleteProductUseCase $useCase
    ): JsonResponse {
        $this->authorize('delete', $product);

        $useCase->execute($product);

        return response()->json(null, 204);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/products/{product}/status",
     *     tags={"Products"},
     *     summary="Update product status",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Product status updated")
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function updateStatus(
        UpdateProductStatusRequest $request,
        Product $product,
        UpdateProductStatusUseCase $useCase
    ): ProductResource {
        $this->authorize('update', $product);
    
        $useCase->execute($product, $request->toDto());
    
        return new ProductResource($product->fresh());
    }
}
