<?php

declare(strict_types=1);

namespace App\Domain\Contracts;

use App\Models\UserNotification;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UserNotificationRepositoryInterface
{
    public function create(array $data): UserNotification;

    public function paginateForUser(
        string $userId,
        int $page,
        int $size,
        bool $onlyUnread = false
    ): LengthAwarePaginator;

    public function unreadCount(string $userId): int;

    public function findForUser(string $notificationId, string $userId): ?UserNotification;

    public function markRead(string $notificationId, string $userId): UserNotification;

    public function markAllRead(string $userId): int;

    public function delete(UserNotification $notification): void;
}