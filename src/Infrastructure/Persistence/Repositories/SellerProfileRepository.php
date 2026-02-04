<?php

declare(strict_types=1);

namespace Src\Infrastructure\Persistence\Repositories;

use Src\Domain\SellerProfiles\Entities\SellerProfile as SellerProfileEntity;
use Src\Domain\SellerProfiles\Repositories\SellerProfileRepositoryInterface;
use Src\Infrastructure\Persistence\Eloquent\Models\SellerProfile as SellerProfileModel;

class SellerProfileRepository implements SellerProfileRepositoryInterface
{
    public function findByUserId(string $userId): ?SellerProfileEntity
    {
        $model = SellerProfileModel::query()->where('user_id', $userId)->first();

        return $model ? $this->toDomain($model) : null;
    }

    public function create(array $attributes): SellerProfileEntity
    {
        $model = SellerProfileModel::query()->create($attributes);

        return $this->toDomain($model);
    }

    public function updateByUserId(string $userId, array $attributes): SellerProfileEntity
    {
        $model = SellerProfileModel::query()->where('user_id', $userId)->firstOrFail();
        $model->fill($attributes);
        $model->save();

        return $this->toDomain($model);
    }

    private function toDomain(SellerProfileModel $model): SellerProfileEntity
    {
        return new SellerProfileEntity(
            id: $model->id,
            userId: $model->user_id,
            storeName: $model->store_name,
            storeDescription: $model->store_description,
            storeBannerUrl: $model->store_banner_url,
            verificationStatus: $model->verification_status,
            ratingAverage: (float) $model->rating_average,
            ratingCount: (int) $model->rating_count,
            vatNumber: $model->vat_number,
            companyName: $model->company_name,
            supportEmail: $model->support_email,
            supportPhone: $model->support_phone,
            categoryTags: $model->category_tags,
            isProSeller: (bool) $model->is_pro_seller
        );
    }
}
