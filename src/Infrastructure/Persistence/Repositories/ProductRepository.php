<?php

declare(strict_types=1);

namespace Src\Infrastructure\Persistence\Repositories;

use DateTimeImmutable;
use Src\Domain\Products\Entities\Product as ProductEntity;
use Src\Domain\Products\Repositories\ProductRepositoryInterface;
use Src\Infrastructure\Persistence\Eloquent\Models\Product as ProductModel;

class ProductRepository implements ProductRepositoryInterface
{
    public function create(array $attributes): ProductEntity
    {
        $model = ProductModel::query()->create($attributes);

        return $this->toDomain($model);
    }

    public function update(string $id, array $attributes): ProductEntity
    {
        $model = ProductModel::query()->findOrFail($id);
        $model->fill($attributes);
        $model->save();

        return $this->toDomain($model);
    }

    public function findById(string $id): ?ProductEntity
    {
        $model = ProductModel::query()
            ->where('id', $id)
            ->where('is_deleted', false)
            ->first();

        return $model ? $this->toDomain($model) : null;
    }

    public function paginateBySellerId(string $sellerId, int $page, int $pageSize): array
    {
        $query = ProductModel::query()
            ->where('seller_id', $sellerId)
            ->where('is_deleted', false)
            ->orderByDesc('created_at');

        $total = (clone $query)->count();
        $items = $query->forPage($page, $pageSize)->get();

        return [
            'items' => $items->map(fn (ProductModel $model): ProductEntity => $this->toDomain($model))->all(),
            'total' => $total,
        ];
    }

    public function softDelete(string $id): void
    {
        $model = ProductModel::query()->findOrFail($id);
        $model->is_deleted = true;
        $model->deleted_at = now();
        $model->save();
    }

    private function toDomain(ProductModel $model): ProductEntity
    {
        $publishedAt = $model->published_at?->toDateTimeImmutable();

        return new ProductEntity(
            id: $model->id,
            sellerId: $model->seller_id,
            title: $model->title,
            description: $model->description,
            categoryId: $model->category_id,
            condition: $model->condition,
            status: $model->status,
            price: (float) $model->price,
            compareAtPrice: $model->compare_at_price !== null ? (float) $model->compare_at_price : null,
            stockQuantity: (int) $model->stock_quantity,
            images: $model->images,
            tags: $model->tags,
            weightKg: $model->weight_kg !== null ? (float) $model->weight_kg : null,
            sku: $model->sku,
            isDigital: (bool) $model->is_digital,
            allowReturns: (bool) $model->allow_returns,
            returnDays: (int) $model->return_days,
            isFeatured: (bool) $model->is_featured,
            publishedAt: $publishedAt instanceof DateTimeImmutable ? $publishedAt : null
        );
    }
}
