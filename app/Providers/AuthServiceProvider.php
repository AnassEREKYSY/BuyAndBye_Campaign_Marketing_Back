<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Product;
use App\Models\User;
use App\Policies\ProductPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Product::class => ProductPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('buyer-only', fn (User $user): bool => $user->role->isBuyer()
        );

        Gate::define('seller-only', fn (User $user): bool => $user->role->isSeller()
        );

        Gate::define('admin-only', fn (User $user): bool => $user->role->isAdmin()
        );

        Gate::define('seller-or-admin', fn (User $user): bool => $user->role->isSellerOrAdmin()
        );
    }
}
