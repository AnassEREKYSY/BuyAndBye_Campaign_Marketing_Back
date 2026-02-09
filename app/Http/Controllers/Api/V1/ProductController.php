<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Enums\ProductStatus;
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
     *
     *     @OA\Parameter(name="page", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="pageSize", in="query", @OA\Schema(type="integer")),
     *
     *     @OA\Response(response=200, description="Products list retrieved"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function index(): JsonResponse
    {
        Gate::authorize('seller-or-admin');

        $page = (int) request()->query('page', 1);
        $pageSize = (int) request()->query('pageSize', 20);

        $products = Product::where('seller_id', auth()->id())
            ->latest()
            ->paginate($pageSize, ['*'], 'page', $page);

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
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"title","condition","price","stock_quantity","is_digital","allow_returns","return_days"},
     *
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="condition", type="string"),
     *             @OA\Property(property="price", type="number"),
     *             @OA\Property(property="stock_quantity", type="integer"),
     *             @OA\Property(property="is_digital", type="boolean"),
     *             @OA\Property(property="allow_returns", type="boolean"),
     *             @OA\Property(property="return_days", type="integer")
     *         )
     *     ),
     *
     *     @OA\Response(response=201, description="Product created"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function store(CreateProductRequest $request): \Illuminate\Http\JsonResponse
    {
        Gate::authorize('seller-or-admin');

        $validated = $request->validated();

        $product = Product::create([
            'seller_id' => auth()->id(),
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'category_id' => $validated['category_id'] ?? null,
            'condition' => $validated['condition'],
            'status' => ProductStatus::Draft->value,
            'price' => $validated['price'],
            'stock_quantity' => $validated['stock_quantity'],
            'images' => $validated['images_data'] ?? null,
            'tags' => $validated['tags'] ?? null,
            'weight_kg' => $validated['weight_kg'] ?? null,
            'sku' => $validated['sku'] ?? null,
            'is_digital' => $validated['is_digital'],
            'allow_returns' => $validated['allow_returns'],
            'return_days' => $validated['return_days'],
        ]);

        return (new ProductResource($product))->response()->setStatusCode(201);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/products/{product}",
     *     tags={"Products"},
     *     summary="Get product by ID",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(name="product", in="path", required=true, @OA\Schema(type="string")),
     *
     *     @OA\Response(response=200, description="Product retrieved"),
     *     @OA\Response(response=404, description="Product not found")
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
     *
     *     @OA\Parameter(name="product", in="path", required=true, @OA\Schema(type="string")),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="price", type="number")
     *         )
     *     ),
     *
     *     @OA\Response(response=200, description="Product updated"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Product not found")
     * )
     */
    public function update(UpdateProductRequest $request, Product $product): ProductResource
    {
        $this->authorize('update', $product);

        $validated = $request->validated();

        $updateData = array_filter([
            'title' => $validated['title'] ?? null,
            'description' => $validated['description'] ?? null,
            'category_id' => $validated['category_id'] ?? null,
            'condition' => $validated['condition'] ?? null,
            'price' => $validated['price'] ?? null,
            'stock_quantity' => $validated['stock_quantity'] ?? null,
            'images' => $validated['images_data'] ?? null,
            'tags' => $validated['tags'] ?? null,
            'weight_kg' => $validated['weight_kg'] ?? null,
            'sku' => $validated['sku'] ?? null,
            'is_digital' => $validated['is_digital'] ?? null,
            'allow_returns' => $validated['allow_returns'] ?? null,
            'return_days' => $validated['return_days'] ?? null,
        ], fn ($value) => $value !== null);

        $product->update($updateData);

        return new ProductResource($product->fresh());
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/products/{product}",
     *     tags={"Products"},
     *     summary="Delete a product",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(name="product", in="path", required=true, @OA\Schema(type="string")),
     *
     *     @OA\Response(response=200, description="Product deleted"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Product not found")
     * )
     */
    public function destroy(Product $product): JsonResponse
    {
        $this->authorize('delete', $product);

        $product->delete();

        return response()->json(['success' => true, 'message' => 'Product deleted']);
    }
}
