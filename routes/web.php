<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return response()->json(['message' => 'Welcome to the Orders App API']);
});

Route::redirect('/', '/login');

Route::fallback(function () {
    return redirect('/login');
});


Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Orders App Routes
    Route::get('/productos', function () {
        return Inertia::render('Products');
    })->name('productos');

    Route::get('/checkout', function () {
        return Inertia::render('Checkout');
    })->name('checkout');

    Route::get('/pedidos', function () {
        return Inertia::render('Orders');
    })->name('pedidos');
});

require __DIR__.'/auth.php';
