<?php

declare(strict_types=1);

namespace App\Policies;

use Src\Domain\Products\Entities\Product;
use Illuminate\Foundation\Auth\User as Authenticatable;

class ProductPolicy
{
    public function view(Authenticatable $user, Product $product): bool
    {
        if ($user->role->isAdmin()) {
            return true;
        }

        if (! $user->role->isSeller()) {
            return false;
        }

        return (string) $product->sellerId() === (string) $user->id;
    }

    public function update(Authenticatable $user, Product $product): bool
    {
        return $this->view($user, $product);
    }

    public function delete(Authenticatable $user, Product $product): bool
    {
        return $this->view($user, $product);
    }
}
