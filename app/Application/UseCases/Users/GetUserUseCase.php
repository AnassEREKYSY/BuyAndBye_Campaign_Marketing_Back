<?php

declare(strict_types=1);

namespace App\Application\UseCases\Users;

use App\Domain\Contracts\UserRepositoryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GetUserUseCase
{
    public function __construct(private readonly UserRepositoryInterface $users) {}

    public function execute(string $userId)
    {
        $user = $this->users->findById($userId);
        if (! $user) throw new NotFoundHttpException('User not found.');

        return $user->load(['brandProfile', 'influencerProfile']);
    }
}