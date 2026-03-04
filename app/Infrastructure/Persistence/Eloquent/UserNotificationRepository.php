<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Contracts\UserNotificationRepositoryInterface;
use App\Models\UserNotification;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserNotificationRepository implements UserNotificationRepositoryInterface
{
    public function create(array $data): UserNotification
    {
        return UserNotification::query()->create($data);
    }

    public function paginateForUser(string $userId, int $page, int $size, bool $onlyUnread = false): LengthAwarePaginator
    {
        $q = UserNotification::query()
            ->where('user_id', $userId)
            ->orderByDesc('created_at');

        if ($onlyUnread) {
            $q->whereNull('read_at');
        }

        return $q->paginate(
            perPage: $size,
            page: $page
        );
    }

    public function unreadCount(string $userId): int
    {
        return (int) UserNotification::query()
            ->where('user_id', $userId)
            ->whereNull('read_at')
            ->count();
    }

    public function findForUser(string $notificationId, string $userId): ?UserNotification
    {
        return UserNotification::query()
            ->where('id', $notificationId)
            ->where('user_id', $userId)
            ->first();
    }

    public function markRead(string $notificationId, string $userId): UserNotification
    {
        $n = $this->findForUser($notificationId, $userId);
        if (! $n) {
            abort(404, 'Notification not found.');
        }

        if ($n->read_at === null) {
            $n->forceFill(['read_at' => now()])->save();
        }

        return $n->fresh();
    }

    public function markAllRead(string $userId): int
    {
        return (int) UserNotification::query()
            ->where('user_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function delete(UserNotification $notification): void
    {
        $notification->delete();
    }
}