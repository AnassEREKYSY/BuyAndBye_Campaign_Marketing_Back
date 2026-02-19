<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [];

    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('admin-only', fn (User $user): bool => $user->role->isAdmin());

        Gate::define('brand-only', fn (User $user): bool => $user->role->isBrand());

        Gate::define('influencer-only', fn (User $user): bool => $user->role->isInfluencer());

        Gate::define('brand-or-admin', fn (User $user): bool => $user->role->isBrand() || $user->role->isAdmin());

        Gate::define('influencer-or-admin', fn (User $user): bool => $user->role->isInfluencer() || $user->role->isAdmin());
    }
}