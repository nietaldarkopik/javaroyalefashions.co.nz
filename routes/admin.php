<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\HeroSlideController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductImageController;
use App\Http\Controllers\Admin\ProductVariantController;
use App\Http\Controllers\Admin\SettingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin panel routes
|--------------------------------------------------------------------------
| Staff/admin only — this is the one part of the app that authenticates.
| Customers never touch anything under /admin.
*/

Route::prefix('admin')->name('admin.')->group(function () {

    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.attempt');

    Route::middleware('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');

        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('pages', PageController::class)->except(['show']);
        Route::resource('categories', CategoryController::class)->except(['show']);
        Route::resource('hero-slides', HeroSlideController::class)->except(['show']);

        Route::resource('products', ProductController::class)->except(['show']);
        Route::delete('products/{product}/images/{image}', [ProductImageController::class, 'destroy'])
            ->name('products.images.destroy');

        Route::post('products/{product}/variants', [ProductVariantController::class, 'store'])
            ->name('products.variants.store');
        Route::put('products/{product}/variants/{variant}', [ProductVariantController::class, 'update'])
            ->name('products.variants.update');
        Route::delete('products/{product}/variants/{variant}', [ProductVariantController::class, 'destroy'])
            ->name('products.variants.destroy');

        Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');
        Route::post('orders/{order}/verify-payment', [OrderController::class, 'verifyPayment'])->name('orders.verify');

        Route::get('customers', [CustomerController::class, 'index'])->name('customers.index');
        Route::get('customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');

        Route::get('settings', [SettingController::class, 'edit'])->name('settings.edit');
        Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
    });
});
