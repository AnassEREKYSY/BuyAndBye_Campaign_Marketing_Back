<?php

declare(strict_types=1);

namespace App\Providers;

use App\Domain\Contracts\UserRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\UserRepository;
use App\Domain\Contracts\ProductRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\ProductRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            UserRepositoryInterface::class,
            UserRepository::class
        );
        $this->app->bind(
            ProductRepositoryInterface::class,
            ProductRepository::class
        );
    }

    public function boot(): void
    {
        //
    }
}
