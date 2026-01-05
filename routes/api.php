<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

// Routes publiques
Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
Route::get('/products', [ProductController::class, "index"])->name('products.index');
Route::get('/products/{id}', [ProductController::class, "show"])->name('products.show');

// Routes publiques pour les catégories
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/{id}', [CategoryController::class, 'show'])->name('categories.show');
Route::get('/categories/{id}/products', [CategoryController::class, 'products'])->name('categories.products');

// Routes publiques pour les médias
Route::get('/products/{productId}/medias', [MediaController::class, 'getProductMedias'])->name('medias.product');
Route::get('/products/{productId}/medias/principal', [MediaController::class, 'getPrincipalMedia'])->name('medias.principal');
Route::get('/products/{productId}/medias/stats', [MediaController::class, 'getMediaStats'])->name('medias.stats');

Route::prefix('shops')->group(function () {
    Route::get('/', [\App\Http\Controllers\ShopController::class, 'index'])->name('shops.index');
    Route::get('/{id}', [\App\Http\Controllers\ShopController::class, 'show'])->name('shops.show');
});

// Routes protégées par Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/products', [ProductController::class, "create"])->name('products.create');
    Route::put('/products/{id}', [ProductController::class, "update"])->name('products.update');
    Route::delete('/products/{id}', [ProductController::class, "delete"])->name('products.delete');

    // Routes protégées pour les catégories
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    // Routes protégées pour les médias
    Route::post('/products/{productId}/medias', [MediaController::class, 'uploadMedia'])->name('medias.upload');
    Route::post('/products/{productId}/medias/multiple', [MediaController::class, 'uploadMultipleMedias'])->name('medias.upload.multiple');
    Route::put('/products/{productId}/medias/{mediaId}/principal', [MediaController::class, 'setAsPrincipal'])->name('medias.set.principal');
    Route::delete('/medias/{mediaId}', [MediaController::class, 'deleteMedia'])->name('medias.delete');
    Route::delete('/products/{productId}/medias', [MediaController::class, 'deleteAllProductMedias'])->name('medias.delete.all');

    Route::prefix('shops')->group(function () {
        Route::post('/', [\App\Http\Controllers\ShopController::class, 'create'])->name('shops.create');
        Route::put('/{id}', [\App\Http\Controllers\ShopController::class, 'update'])->name('shops.update');
        Route::delete('/{id}', [\App\Http\Controllers\ShopController::class, 'delete'])->name('shops.delete');
    });

    // Routes pour les commandes
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');

    // Routes pour les utilisateurs
    Route::apiResource('users', UserController::class)->except(['create', 'edit']);

    Route::get('auth/user', [AuthController::class, 'getUserConnected'])->name('auth.user');
    Route::get('/logout', [AuthController::class, 'logout'])->name('auth.logout');
});