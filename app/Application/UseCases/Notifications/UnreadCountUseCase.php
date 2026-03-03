<?php

declare(strict_types=1);

namespace App\Application\UseCases\Notifications;

use App\Domain\Contracts\UserNotificationRepositoryInterface;
use App\Models\User;

class UnreadCountUseCase
{
    public function __construct(
        private readonly UserNotificationRepositoryInterface $notifications
    ) {}

    public function execute(User $user): int
    {
        return $this->notifications->unreadCount($user->id);
    }
}