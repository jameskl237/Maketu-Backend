<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/products', [ProductController::class, "index"])->name('products.index');
Route::get('/products/{id}', [ProductController::class, "show"])->name('products.show');
Route::post('/products', [ProductController::class, "create"])->name('products.create');

Route::prefix('shops')->group(function () {
    Route::get('/', [\App\Http\Controllers\ShopController::class, 'index'])->name('shops.index');
    Route::get('/{id}', [\App\Http\Controllers\ShopController::class, 'show'])->name('shops.show');
    Route::post('/', [\App\Http\Controllers\ShopController::class, 'create'])->name('shops.create');
    Route::put('/{id}', [\App\Http\Controllers\ShopController::class, 'update'])->name('shops.update');
    Route::delete('/{id}', [\App\Http\Controllers\ShopController::class, 'delete'])->name('shops.delete');
});

Route::post('/login', [AuthController::class, 'login'])->name('auth.login');