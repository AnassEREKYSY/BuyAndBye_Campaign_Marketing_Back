<?php

declare(strict_types=1);

namespace Src\Infrastructure\Persistence\Repositories;

use Src\Domain\Products\Entities\Product as DomainProduct;
use Src\Domain\Products\Repositories\ProductRepositoryInterface;
use Src\Infrastructure\Persistence\Eloquent\Models\Product as EloquentProduct;

class ProductRepository implements ProductRepositoryInterface
{
    public function findById(string $id): ?DomainProduct
    {
        $model = EloquentProduct::query()->find($id);

        return $model ? $this->toDomain($model) : null;
    }

    public function create(array $data): DomainProduct
    {
        $model = EloquentProduct::create($data);

        return $this->toDomain($model);
    }

    public function update(string $id, array $data): DomainProduct
    {
        $model = EloquentProduct::query()->findOrFail($id);
        $model->update($data);

        return $this->toDomain($model->refresh());
    }

    public function paginateBySellerId(
        string $sellerId,
        int $page,
        int $pageSize
    ): array {
        $paginator = EloquentProduct::query()
            ->where('seller_id', $sellerId)
            ->latest()
            ->paginate(
                perPage: $pageSize,
                page: $page
            );

        return [
            'items' => array_map(
                fn (EloquentProduct $model) => $this->toDomain($model),
                $paginator->items()
            ),
            'total' => $paginator->total(),
        ];
    }

    public function softDelete(string $id): void
    {
        EloquentProduct::query()
            ->where('id', $id)
            ->delete();
    }

    private function toDomain(EloquentProduct $model): DomainProduct
    {
        return new DomainProduct(
            id: $model->id,
            sellerId: $model->seller_id,
            title: $model->title,
            description: $model->description,
            categoryId: $model->category_id,
            condition: $model->condition,
            status: $model->status,
            price: (float) $model->price,
            compareAtPrice: $model->compare_at_price !== null
                ? (float) $model->compare_at_price
                : null,
            stockQuantity: (int) $model->stock_quantity,
            images: $model->images,
            tags: $model->tags,
            weightKg: $model->weight_kg !== null
                ? (float) $model->weight_kg
                : null,
            sku: $model->sku,
            isDigital: (bool) $model->is_digital,
            allowReturns: (bool) $model->allow_returns,
            returnDays: (int) $model->return_days,
            isFeatured: (bool) $model->is_featured,
            publishedAt: $model->published_at
                ? $model->published_at->toDateTimeImmutable()
                : null
        );
    }    
}
