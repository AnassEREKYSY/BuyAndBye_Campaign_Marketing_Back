<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    public function view(User $user, Product $product): bool
    {
        if ($user->role->isAdmin()) {
            return true;
        }

        if (! $user->role->isSeller()) {
            return false;
        }

        return $product->seller_id === $user->id;
    }

    public function update(User $user, Product $product): bool
    {
        return $this->view($user, $product);
    }

    public function delete(User $user, Product $product): bool
    {
        return $this->view($user, $product);
    }
}
