<?php

declare(strict_types=1);

namespace App\Domain\Contracts;

use App\Models\TrackingLink;

interface TrackingLinkRepositoryInterface
{
    public function findByCode(string $code): ?TrackingLink;

    public function findByCollaborationId(string $collaborationId): ?TrackingLink;

    public function create(array $data): TrackingLink;
}