<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

// unauthenticated endpoints
Route::get('/', [HomeController::class, 'index'])->name('shop.home.index');
Route::get('/about', [HomeController::class, 'products'])->name('shop.home.about');
Route::get('/contact', [HomeController::class, 'contact'])->name('shop.home.contact');
Route::get('/products', [HomeController::class, 'products'])->name('shop.home.products');
Route::get('/categories', [HomeController::class, 'categories'])->name('shop.home.categories');
Route::get('/product/{id}', [HomeController::class, 'product'])->name('shop.home.product');
Route::get('/category/{id}', [HomeController::class, 'category'])->name('shop.home.category');

// /authenticated endpoints
Route::get('/login', [HomeController::class, 'index'])->name('login');

// /cart
Route::prefix('cart')->group(function () {
    Route::post('/add/{id}', [CartController::class, 'addToCart'])->name('shop.home.cart.add');
});
