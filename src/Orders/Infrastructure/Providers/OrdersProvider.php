<?php

namespace Src\Orders\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Src\Orders\Domain\Interfaces\OrdersRepositoryInterface;
use Src\Orders\Domain\Interfaces\ProductsRepositoryInterface;
use Src\Orders\Infrastructure\Repositories\OrdersRepository;
use Src\Orders\Infrastructure\Repositories\ProductsRepository;

class OrdersProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(OrdersRepositoryInterface::class, OrdersRepository::class);
        $this->app->singleton(ProductsRepositoryInterface::class, ProductsRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../Routes/api.php');
    }
}
