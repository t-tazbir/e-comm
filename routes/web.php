<?php

use App\Http\Controllers\StorefrontController;
use Illuminate\Support\Facades\Route;

Route::get('/', [StorefrontController::class, 'home'])->name('home');
Route::get('/product/{slug}', [StorefrontController::class, 'showProduct'])->where('slug', '[a-z0-9-]+')->name('products.show');
Route::get('/cart', [StorefrontController::class, 'cart'])->name('cart.show');
Route::post('/cart/items/{slug}', [StorefrontController::class, 'addToCart'])->where('slug', '[a-z0-9-]+')->name('cart.items.store');
Route::patch('/cart/items/{slug}', [StorefrontController::class, 'updateCart'])->where('slug', '[a-z0-9-]+')->name('cart.items.update');
Route::delete('/cart/items/{slug}', [StorefrontController::class, 'removeFromCart'])->where('slug', '[a-z0-9-]+')->name('cart.items.destroy');
Route::get('/checkout', [StorefrontController::class, 'checkout'])->name('checkout.show');
Route::post('/checkout', [StorefrontController::class, 'submitCheckout'])->name('checkout.store');
Route::get('/order/confirmation', [StorefrontController::class, 'orderConfirmation'])->name('order.confirmation');
