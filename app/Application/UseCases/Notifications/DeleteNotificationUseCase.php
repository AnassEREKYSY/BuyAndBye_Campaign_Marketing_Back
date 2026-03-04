<?php

declare(strict_types=1);

namespace App\Application\UseCases\Notifications;

use App\Domain\Contracts\UserNotificationRepositoryInterface;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DeleteNotificationUseCase
{
    public function __construct(
        private readonly UserNotificationRepositoryInterface $notifications
    ) {}

    public function execute(User $user, string $id): void
    {
        $n = $this->notifications->findForUser($id, $user->id);

        if (! $n) {
            throw new ModelNotFoundException();
        }

        $this->notifications->delete($n);
    }
}