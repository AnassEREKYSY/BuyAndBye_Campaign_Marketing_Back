<?php

declare(strict_types=1);

namespace App\Application\UseCases\Notifications;

use App\Domain\Contracts\UserNotificationRepositoryInterface;
use App\Models\User;

class ListNotificationsUseCase
{
    public function __construct(
        private readonly UserNotificationRepositoryInterface $notifications
    ) {}

    public function execute(User $user, int $page, int $size, bool $onlyUnread = false)
    {
        return $this->notifications->paginateForUser($user->id, $page, $size, $onlyUnread);
    }
}