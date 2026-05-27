<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OnepageController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SessionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerProfileDashboardController;

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
        Route::post('profile/avatar', [CustomerProfileDashboardController::class, 'avatar'])->name('customer.profile.avatar');
        Route::get('customer/profile', [CustomerProfileDashboardController::class, 'index'])->name('customer.profile.index');
        Route::post('profile/store', [CustomerProfileDashboardController::class, 'store'])
            ->name('customer.profile.store');
        Route::get('customer/orders', [OrderController::class, 'index'])->name('customer.orders.index');
        Route::get('customer/order/{id}', [OrderController::class, 'view'])->name('customer.orders.view');


        Route::delete('logout', [SessionController::class, 'destroy'])->name('customer.session.destroy');
    });

    // Shopping Cart Routes
    Route::prefix('checkout/cart')->group(function () {
        Route::get('', [CartController::class, 'index'])->name('shop.checkout.cart.index');
        Route::get('mini-cart', [CartController::class, 'index'])->name('shop.checkout.cart.mini-cart');
        Route::post('add/{id}', [CartController::class, 'add'])->name('shop.cart.add');
        Route::delete('remove/{id}', [CartController::class, 'remove'])->name('shop.cart.remove');
        Route::post('update', [CartController::class, 'update'])->name('shop.cart.update');
    });

    // Checkout Flow Routes
    Route::prefix('checkout/onepage')->group(function () {
        Route::get('', [OnepageController::class, 'index'])->name('shop.checkout.onepage.index');
        Route::post('save-address', [OnepageController::class, 'saveAddress'])->name('shop.checkout.save_address');
        Route::post('save-shipping', [OnepageController::class, 'saveShipping'])->name('shop.checkout.save_shipping');
        Route::post('save-payment', [OnepageController::class, 'savePayment'])->name('shop.checkout.save_payment');
        Route::post('save-order', [OnepageController::class, 'saveOrder'])->name('shop.checkout.save_order');
        Route::get('success', [OnepageController::class, 'success'])->name('shop.checkout.success');
    });

    // Custom M-Pesa STK Push Route
    Route::post('mpesa/stk-push', [App\Http\Controllers\MpesaController::class, 'stkPush'])
        ->name('api.mpesa.stkpush');

});

// /cart
Route::prefix('cart')->group(function () {
    Route::post('/add/{id}', [CartController::class, 'add'])->name('shop.cart.add');
});
