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
//    Route::get('/', function () {
//        return view('welcome',
//            ['user' => auth()->user()]);
//    })->name('home');

    Route::post('/logout', function () {
        auth()->logout();

        return redirect()->route('login');
    })->name('logout');
});

Route::middleware('auth.seller')->group(function () {
    Route::prefix('seller')->group(function () {

        Route::controller(ProductController::class)->group(function () {
            Route::prefix('products')->group(function () {
                Route::get('/', 'index')->name('seller.products.index');

                Route::get('/create', 'create')->name('seller.products.create');

                Route::post('/', 'store')->name('seller.products.store');

                Route::get('/{product}', 'show')->name('seller.products.show');
            });
        });

        Route::get('/dashboard', function () {
            return view('seller.dashboard');
        })->name('seller.dashboard');
    });
});

Route::get('/', [CatalogueController::class, 'home'])->name('home');
