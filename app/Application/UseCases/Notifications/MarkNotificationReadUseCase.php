<?php

declare(strict_types=1);

namespace App\Application\UseCases\Notifications;

use App\Domain\Contracts\UserNotificationRepositoryInterface;
use App\Models\User;

class MarkNotificationReadUseCase
{
    public function __construct(
        private readonly UserNotificationRepositoryInterface $notifications
    ) {}

    public function execute(User $user, string $notificationId)
    {
        return $this->notifications->markRead($notificationId, $user->id);
    }
}