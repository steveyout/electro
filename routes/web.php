<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SessionController;
use Illuminate\Support\Facades\Route;

// unauthenticated endpoints
Route::get('/', [HomeController::class, 'index'])->name('shop.home.index');
Route::get('/about', [HomeController::class, 'products'])->name('shop.home.about');
Route::get('/contact', [HomeController::class, 'contact'])->name('shop.home.contact');
Route::get('/products', [HomeController::class, 'products'])->name('shop.home.products');
Route::get('/categories', [HomeController::class, 'categories'])->name('shop.home.categories');
Route::get('/product/{id}', [HomeController::class, 'product'])->name('shop.home.product');
Route::get('/category/{id}', [HomeController::class, 'category'])->name('shop.home.category');
Route::get('/search', [SearchController::class, 'index'])->name('shop.search.index');

// /authenticated endpoints
// Customer Login & Session Routes
Route::group(['middleware' => ['web', 'locale', 'theme', 'currency']], function () {

    // Guest Routes
    Route::middleware('guest:customer')->group(function () {
        Route::get('login', [SessionController::class, 'showLoginForm'])->name('customer.session.index');
        Route::post('login', [SessionController::class, 'create'])->name('customer.session.create');
        Route::get('register', [SessionController::class, 'showRegistrationForm'])->name('customer.register.index');
        Route::post('register', [SessionController::class, 'register'])->name('customer.register.create');
    });

    // Authenticated Routes
    Route::middleware('auth:customer')->group(function () {
        // missing routes causing your error:
        Route::get('customer/profile', function () {
            return 'Profile Page Coming Soon';
        })->name('customer.profile.index');

        Route::get('customer/orders', function () {
            return 'Orders Page Coming Soon';
        })->name('customer.orders.index');

        Route::delete('logout', [SessionController::class, 'destroy'])->name('customer.session.destroy');
    });
});

// /cart
Route::prefix('cart')->group(function () {
    Route::post('/add/{id}', [CartController::class, 'add'])->name('shop.cart.add');
});
