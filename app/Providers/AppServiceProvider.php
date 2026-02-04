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

// Infrastructure Services
use Src\Infrastructure\Services\EmailService;
use Src\Infrastructure\Services\ImageUploadService;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Repository Bindings
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(AuthRepositoryInterface::class, AuthRepository::class);

        // Service Bindings
        $this->app->singleton(EmailService::class);
        $this->app->singleton(ImageUploadService::class);
    }

    public function boot(): void
    {
        //
    }
}