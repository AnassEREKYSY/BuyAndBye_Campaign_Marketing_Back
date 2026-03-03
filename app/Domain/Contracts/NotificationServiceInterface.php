<?php

declare(strict_types=1);

namespace App\Domain\Contracts;

use App\Models\UserNotification;

interface NotificationServiceInterface
{
    public function notify(
        string $userId,
        string $type,
        string $title,
        ?string $body = null,
        ?array $data = null,
        ?string $actorId = null,
        ?string $entityType = null,
        ?string $entityId = null,
    ): UserNotification;
}