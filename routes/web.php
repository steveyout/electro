<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('shop.home.index');
Route::get('/product/{id}', [HomeController::class, 'product'])->name('shop.home.product');
