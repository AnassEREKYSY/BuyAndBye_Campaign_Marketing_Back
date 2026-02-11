<?php

declare(strict_types=1);

namespace App\Domain\Contracts;

use App\Models\User;

interface UserRepositoryInterface
{
    public function findByEmail(string $email): ?User;

    public function save(User $user): void;

    public function update(User $user, array $data): void;
}

