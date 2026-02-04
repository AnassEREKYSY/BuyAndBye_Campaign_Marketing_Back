<?php

declare(strict_types=1);

namespace App\Policies;

use Src\Domain\Shared\Enums\UserRole;
use Src\Domain\Users\Entities\User as UserEntity;
use Src\Infrastructure\Persistence\Eloquent\Models\User;

class UserPolicy
{
    public function view(User $actor, UserEntity $target): bool
    {
        return $this->isAdmin($actor) || $actor->id === $target->id;
    }

    public function update(User $actor, UserEntity $target): bool
    {
        return $this->isAdmin($actor) || $actor->id === $target->id;
    }

    private function isAdmin(User $actor): bool
    {
        return (int) $actor->role === UserRole::Admin->value;
    }
}
