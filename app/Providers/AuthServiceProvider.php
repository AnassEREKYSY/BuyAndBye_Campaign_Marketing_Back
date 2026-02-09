<?php

declare(strict_types=1);

namespace App\Providers;

use App\Policies\ProductPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Src\Domain\Products\Entities\Product;
use Src\Domain\Shared\Enums\UserRole;
use Src\Domain\Users\Entities\User as UserEntity;
use Src\Infrastructure\Persistence\Eloquent\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        UserEntity::class => UserPolicy::class,
        Product::class => ProductPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('buyer-only', fn (User $user): bool =>
            $user->role->isBuyer()
        );

        Gate::define('seller-only', fn (User $user): bool =>
            $user->role === UserRole::Seller
        );

        Gate::define('admin-only', fn (User $user): bool =>
            $user->role->isAdmin()
        );

        Gate::define('seller-or-admin', fn (User $user): bool =>
            $user->role->isSellerOrAdmin()
        );
    }
}
