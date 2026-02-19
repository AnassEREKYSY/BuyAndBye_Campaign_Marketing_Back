<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Application\UseCases\Product\CreateProductUseCase;
use App\Application\UseCases\Product\DeleteProductUseCase;
use App\Application\UseCases\Product\ListBrandProductsUseCase;
use App\Application\UseCases\Product\UpdateProductUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Products", description="Brand products management")
 */
class ProductController extends Controller
{
    private function user(): User
    {
        /** @var User */
        return Auth::user();
    }

    /**
     * @OA\Get(
     *     path="/api/v1/products",
     *     tags={"Products"},
     *     summary="List brand products (paginated)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer", example=1)),
     *     @OA\Parameter(name="size", in="query", required=false, @OA\Schema(type="integer", example=20)),
     *     @OA\Response(response=200, description="Products list"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function index(ListBrandProductsUseCase $useCase): AnonymousResourceCollection
    {
        Gate::authorize('brand-only');

        $page = (int) request()->query('page', 1);
        $size = (int) request()->query('size', 20);

        $products = $useCase->execute($this->user(), $page, $size);

        return ProductResource::collection($products);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/products",
     *     tags={"Products"},
     *     summary="Create a product",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Product A"),
     *             @OA\Property(property="description", type="string", example="Description"),
     *             @OA\Property(property="price", type="number", format="float", example=199.99),
     *             @OA\Property(property="currency", type="string", example="MAD"),
     *             @OA\Property(property="landing_url", type="string", format="uri", example="https://brand.com/product-a"),
     *             @OA\Property(property="images", type="array", @OA\Items(type="string", example="https://.../img.png"))
     *         )
     *     ),
     *     @OA\Response(response=201, description="Product created"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(CreateProductRequest $request, CreateProductUseCase $useCase): JsonResponse
    {
        Gate::authorize('brand-only');

        $product = $useCase->execute($this->user(), $request->toDto());

        return (new ProductResource($product))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/products/{id}",
     *     tags={"Products"},
     *     summary="Get a product by id",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string", format="uuid")),
     *     @OA\Response(response=200, description="Product returned"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function show(Product $product): ProductResource
    {
        Gate::authorize('brand-only');

        if ($product->brand_id !== $this->user()->id) {
            abort(403, 'Forbidden');
        }

        return new ProductResource($product);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/products/{id}",
     *     tags={"Products"},
     *     summary="Update a product",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string", format="uuid")),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Updated name"),
     *             @OA\Property(property="description", type="string", example="Updated description"),
     *             @OA\Property(property="price", type="number", format="float", example=99.99),
     *             @OA\Property(property="currency", type="string", example="MAD"),
     *             @OA\Property(property="landing_url", type="string", format="uri", example="https://brand.com/product-a"),
     *             @OA\Property(property="status", type="string", enum={"draft","active","archived"}, example="active"),
     *             @OA\Property(property="images", type="array", @OA\Items(type="string"))
     *         )
     *     ),
     *     @OA\Response(response=200, description="Product updated"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Not found"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function update(string $id, UpdateProductRequest $request, UpdateProductUseCase $useCase): ProductResource
    {
        Gate::authorize('brand-only');

        $product = $useCase->execute($this->user(), $id, $request->toDto());

        return new ProductResource($product);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/products/{id}",
     *     tags={"Products"},
     *     summary="Delete a product (soft delete)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string", format="uuid")),
     *     @OA\Response(response=200, description="Product deleted"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function destroy(string $id, DeleteProductUseCase $useCase): JsonResponse
    {
        Gate::authorize('brand-only');

        $useCase->execute($this->user(), $id);

        return response()->json(['success' => true]);
    }
}