<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// Domain Repositories
use Src\Domain\Users\Repositories\UserRepositoryInterface;
use Src\Infrastructure\Persistence\Repositories\UserRepository;

use Src\Domain\Products\Repositories\ProductRepositoryInterface;
use Src\Infrastructure\Persistence\Repositories\ProductRepository;

use Src\Domain\Auth\Repositories\AuthRepositoryInterface;
use Src\Infrastructure\Persistence\Repositories\AuthRepository;

// Domain Services
use Src\Domain\Auth\Services\AuthService;
use Src\Domain\Products\Services\ProductsService;
use Src\Domain\Users\Services\UsersService;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Repository Bindings
        $this->app->bind(
            UserRepositoryInterface::class,
            UserRepository::class
        );

        $this->app->bind(
            ProductRepositoryInterface::class,
            ProductRepository::class
        );

        $this->app->bind(
            AuthRepositoryInterface::class,
            AuthRepository::class
        );

        // Service Bindings
        $this->app->singleton(AuthService::class);
        $this->app->singleton(ProductsService::class);
        $this->app->singleton(UsersService::class);
    }

    public function boot(): void
    {
        //
    }
}