<?php

declare(strict_types=1);

namespace App\Policies;

use Src\Application\Products\DTOs\ProductResponse;
use Src\Domain\Shared\Enums\ProductStatus;
use Src\Domain\Shared\Enums\UserRole;
use Src\Infrastructure\Persistence\Eloquent\Models\User;

class ProductPolicy
{
    public function view(?User $user, ProductResponse $product): bool
    {
        if ($user && ((int) $user->role === UserRole::Admin->value)) {
            return true;
        }

        if ($user && $user->id === $product->sellerId) {
            return true;
        }

        return $product->status === ProductStatus::Active->value;
    }

    public function update(User $user, ProductResponse $product): bool
    {
        return $this->isAdmin($user) || $user->id === $product->sellerId;
    }

    public function delete(User $user, ProductResponse $product): bool
    {
        return $this->isAdmin($user) || $user->id === $product->sellerId;
    }

    private function isAdmin(User $user): bool
    {
        return (int) $user->role === UserRole::Admin->value;
    }
}
