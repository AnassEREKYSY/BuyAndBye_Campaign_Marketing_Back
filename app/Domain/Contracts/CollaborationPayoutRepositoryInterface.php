<?php

declare(strict_types=1);

namespace App\Domain\Contracts;

use App\Enums\PayoutStatus;
use App\Models\CollaborationPayout;
use Illuminate\Support\Collection;

interface CollaborationPayoutRepositoryInterface
{
    public function create(array $data): CollaborationPayout;

    public function findById(string $id): ?CollaborationPayout;

    public function updateStatus(CollaborationPayout $payout, PayoutStatus $status): void;

    public function listForInfluencer(string $influencerId): Collection;

    public function existsForPeriod(string $collaborationId, string $start, string $end): bool;
}