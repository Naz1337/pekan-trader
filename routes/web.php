<?php

use App\Http\Controllers\OrderPaymentController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CatalogueController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SellerProfileController;

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

                Route::post('/{product}/attributes', 'storeAttribute')->name('seller.products.attributes.store');
                Route::delete('/{product}/attributes/{attribute}', 'destroyAttribute')->name('seller.products.attributes.destroy');
            });
        });

        Route::controller(OrderController::class)->group(function () {
            Route::prefix('orders')->group(function () {
                Route::get('/', 'seller_index')->name('seller.orders.index');

                Route::get('/{order}', 'seller_show')->name('seller.orders.show');

                Route::patch('/{order}/cancel', 'cancel')->name('seller.orders.cancel');

                Route::patch('/{order}/set_delivering', 'setDelivering')->name(
                    'seller.orders.setDelivering'
                );

            });
        });

        Route::controller(OrderPaymentController::class)->group(function () {
            Route::prefix('order_payments')->group(function() {
                Route::patch('/accept/{orderPayment}', 'accept')->name('seller.order_payments.accept');
                Route::patch('/reject/{orderPayment}', 'reject')->name('seller.order_payments.reject');
            });
        });

        Route::get('/dashboard', function () {
            return view('seller.dashboard');
        })->name('seller.dashboard');
    });
});

Route::controller(CatalogueController::class)->group(function () {
    Route::get('/', 'home')->name('home');

    Route::prefix('products')->group(function () {
       Route::get('/{product}', 'show')->name('catalogue.show');

       Route::post('/{product}/add_to_cart', 'add_to_cart')->name('catalogue.add_to_cart')
           ->middleware('auth');
    });

    Route::middleware('auth')->group(function () {
        Route::get('/cart', 'show_cart')->name('cart.show');
        Route::post('/cart/checkout/place_order', 'place_order')->name('order.place');
        Route::post('/cart/checkout', 'show_checkout')->name('checkout.show');
        Route::delete('/cart/{product}', 'remove_from_cart')->name('cart.remove');
    });
});

Route::get('/sellers/{seller}', [SellerProfileController::class, 'show'])->name('seller.profile.show');

Route::middleware('auth')->group(function () {
    Route::controller(OrderController::class)->group(function () {
        Route::prefix('orders')->group(function () {
            Route::get('/', 'index')->name('orders.index');
            Route::get('/{order}', 'show')->name('orders.show');
            Route::post('/{order}/pay', 'pay')->name('order.pay');

            Route::patch('/{order}/set_received', 'setReceived')->name('orders.setReceived');
        });
    });
});
