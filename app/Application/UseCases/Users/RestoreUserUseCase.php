<?php

declare(strict_types=1);

namespace App\Application\UseCases\Users;

use App\Domain\Contracts\UserRepositoryInterface;
use App\Enums\AccountStatus;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RestoreUserUseCase
{
    public function __construct(private readonly UserRepositoryInterface $users) {}

    public function execute(string $userId): void
    {
        $user = $this->users->restore($userId);
        if (! $user) throw new NotFoundHttpException('User not found.');

        // Option: keep previous status, or set Active by default
        $this->users->update($user, ['status' => AccountStatus::Active]);
    }
}