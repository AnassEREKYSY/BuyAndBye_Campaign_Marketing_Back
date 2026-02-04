<?php

declare(strict_types=1);

namespace Src\Domain\Users\Repositories;

use Src\Domain\Users\Entities\User;

interface UserRepositoryInterface
{
    public function create(array $attributes): User;

    public function update(string $id, array $attributes): User;

    public function findById(string $id): ?User;

    public function findByEmail(string $email): ?User;
}
