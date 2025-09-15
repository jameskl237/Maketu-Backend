<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/products', [ProductController::class, "index"])->name('products.index');
Route::get('/products/{id}', [ProductController::class, "show"])->name('products.show');
Route::post('/products', [ProductController::class, "create"])->name('products.create');