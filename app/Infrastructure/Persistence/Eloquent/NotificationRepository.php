<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Contracts\NotificationRepositoryInterface;
use App\Models\Notification;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class NotificationRepository implements NotificationRepositoryInterface
{
    public function create(array $data): Notification
    {
        return Notification::create($data);
    }

    public function findById(string $id): ?Notification
    {
        return Notification::query()->where('id', $id)->first();
    }

    public function paginateForUser(string $userId, int $page, int $size): LengthAwarePaginator
    {
        return Notification::query()
            ->where('user_id', $userId)
            ->orderByDesc('created_at')
            ->paginate(perPage: $size, page: $page);
    }

    public function unreadCount(string $userId): int
    {
        return Notification::query()
            ->where('user_id', $userId)
            ->whereNull('read_at')
            ->count();
    }

    public function markRead(Notification $notification): void
    {
        if ($notification->read_at === null) {
            $notification->update(['read_at' => now()]);
        }
    }

    public function markAllRead(string $userId): int
    {
        return Notification::query()
            ->where('user_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }
}