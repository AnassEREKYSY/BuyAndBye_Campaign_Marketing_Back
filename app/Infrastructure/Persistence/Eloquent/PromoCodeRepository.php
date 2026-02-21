<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Contracts\PromoCodeRepositoryInterface;
use App\Models\PromoCode;

class PromoCodeRepository implements PromoCodeRepositoryInterface
{
    public function findByCode(string $code): ?PromoCode
    {
        return PromoCode::query()
            ->with(['collaboration.campaign.product', 'collaboration.influencer'])
            ->where('code', $code)
            ->first();
    }

    public function findByCollaborationId(string $collaborationId): ?PromoCode
    {
        return PromoCode::query()->where('collaboration_id', $collaborationId)->first();
    }

    public function create(array $data): PromoCode
    {
        return PromoCode::create($data);
    }
}