<?php

declare(strict_types=1);

namespace App\Services\Notifications;

use App\Domain\Contracts\NotificationServiceInterface;
use App\Domain\Contracts\UserNotificationRepositoryInterface;
use App\Models\UserNotification;
use Illuminate\Support\Str;

class NotificationService implements NotificationServiceInterface
{
    public function __construct(
        private readonly UserNotificationRepositoryInterface $notifications
    ) {}

    public function notify(
        string $userId,
        string $type,
        string $title,
        ?string $body = null,
        ?array $data = null,
        ?string $actorId = null,
        ?string $entityType = null,
        ?string $entityId = null,
    ): UserNotification {
        return $this->notifications->create([
            'id' => (string) Str::uuid(),
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'body' => $body,
            'actor_id' => $actorId,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'data' => $data,
        ]);
    }
}