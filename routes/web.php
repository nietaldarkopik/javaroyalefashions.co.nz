<?php

use App\Http\Controllers\Front\CartController;
use App\Http\Controllers\Front\CategoryController;
use App\Http\Controllers\Front\CheckoutController;
use App\Http\Controllers\Front\HomeController;
use App\Http\Controllers\Front\OrderReviewController;
use App\Http\Controllers\Front\PageController;
use App\Http\Controllers\Front\ProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Storefront routes
|--------------------------------------------------------------------------
| Guest-only: nothing here sits behind auth, there is no customer login.
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');

Route::get('/categories/{slug}', [CategoryController::class, 'show'])->name('categories.show');

Route::get('/page/{slug}', [PageController::class, 'show'])->name('pages.show');

Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add', [CartController::class, 'add'])->name('add');
    Route::post('/update', [CartController::class, 'update'])->name('update');
    Route::delete('/{lineKey}', [CartController::class, 'remove'])->name('remove');
});

Route::prefix('checkout')->name('checkout.')->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->name('index');
    Route::post('/', [CheckoutController::class, 'store'])->name('store');
    Route::get('/{orderNumber}/success', [CheckoutController::class, 'success'])->name('success');
    Route::post('/{orderNumber}/proof', [CheckoutController::class, 'uploadProof'])->name('proof');
});

// Order review page, reached only via the emailed invitation link.
Route::prefix('review/order')->name('reviews.order.')->where(['token' => '[A-Za-z0-9]{64}'])->group(function () {
    Route::get('/{token}', [OrderReviewController::class, 'show'])->middleware('throttle:review-page')->name('show');
    Route::post('/{token}', [OrderReviewController::class, 'store'])->middleware('throttle:review-submit')->name('store');
});

require __DIR__.'/admin.php';
