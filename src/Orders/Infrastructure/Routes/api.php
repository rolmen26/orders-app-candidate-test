<?php

use Illuminate\Support\Facades\Route;
use Src\Orders\Infrastructure\Http\Controllers\ProductsController;
use Src\Orders\Infrastructure\Http\Controllers\OrdersController;

Route::prefix('api')
    ->group(function (): void {
        Route::prefix('/productos')
            ->group(function (): void {
                Route::get('/', [ProductsController::class, 'index']);
                Route::post('/', [ProductsController::class, 'store']);
                Route::put('/{id}', [ProductsController::class, 'update']);
                Route::delete('/{id}', [ProductsController::class, 'destroy']);
            });

        Route::prefix('/pedidos')
            ->group(function (): void {
                Route::post('/', [OrdersController::class, 'store']);
                Route::get('/', [OrdersController::class, 'index']);
                Route::get('/{id}', [OrdersController::class, 'show']);
            });
    });
