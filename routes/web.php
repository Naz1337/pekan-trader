<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CatalogueController;

Route::middleware('guest')->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::get('/login', 'login')
            ->name('login');

        Route::post('/login', 'loginPost')
            ->name('login.post');

        Route::get('/register', 'register')
            ->name('register');
        Route::post('/register', 'registerPost')
            ->name('register.post');

        Route::post('/register/seller/submit', 'postRegisterSellerStep')
            ->name('register.seller.submit');
        Route::get('/register/seller', 'showRegisterSellerStep')
            ->name('register.seller');
    });

});

// guard for normal users
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::middleware('auth.seller')->group(function () {
    Route::prefix('seller')->group(function () {

        Route::controller(ProductController::class)->group(function () {
            Route::prefix('products')->group(function () {
                Route::get('/', 'index')->name('seller.products.index');

                Route::get('/create', 'create')->name('seller.products.create');

                Route::post('/', 'store')->name('seller.products.store');

                Route::get('/{product}', 'show')->name('seller.products.show');

                Route::get('/{product}/edit', 'edit')->name('seller.products.edit');

                Route::put('/{product}', 'update')->name('seller.products.update');

                Route::delete('/{product}', 'destroy')->name('seller.products.destroy');
            });
        });

        Route::get('/dashboard', function () {
            return view('seller.dashboard');
        })->name('seller.dashboard');
    });
});

Route::get('/', [CatalogueController::class, 'home'])->name('home');

Route::controller(CatalogueController::class)->group(function () {
    Route::get('/', 'home')->name('home');

    Route::prefix('products')->group(function () {
       Route::get('/{product}', 'show')->name('catalogue.show');
    });
});
