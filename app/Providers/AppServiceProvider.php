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
use Src\Domain\Auth\Services\AuthServiceInterface;
use Src\Domain\Users\Services\UserContextInterface;
use Src\Infrastructure\Services\SanctumUserContext;

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

        $this->app->bind(
            UserContextInterface::class,
            SanctumUserContext::class
        );

        $this->app->singleton(
            \Src\Domain\Auth\Services\AuthServiceInterface::class,
            \Src\Domain\Auth\Services\AuthService::class
        );

        // Service Bindings
        $this->app->singleton(ProductsService::class);
        $this->app->singleton(UsersService::class);
    }

    public function boot(): void
    {
        //
    }
}