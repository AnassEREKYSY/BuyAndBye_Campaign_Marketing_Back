<?php

declare(strict_types=1);

namespace App\Domain\Contracts;

use App\Models\PromoCode;

interface PromoCodeRepositoryInterface
{
    public function findByCode(string $code): ?PromoCode;

    public function findByCollaborationId(string $collaborationId): ?PromoCode;

    public function create(array $data): PromoCode;
}