<?php

use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductInquiryController;
use App\Http\Middleware\EnsureAdminKey;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/urunler', [ProductController::class, 'index'])->name('products.index');

Route::get('/urunler/{product}', [ProductController::class, 'show'])->name('products.show');

Route::post('/urunler/{product}/bilgi-talebi', [ProductInquiryController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('products.inquiries.store');

Route::prefix('yonetim/{adminKey}')
    ->middleware(EnsureAdminKey::class)
    ->name('admin.')
    ->group(function () {
        Route::get('/', fn () => to_route('admin.products.index'))->name('home');

        Route::resource('urunler', AdminProductController::class)
            ->except('show')
            ->parameters(['urunler' => 'product'])
            ->names('products');
    });
