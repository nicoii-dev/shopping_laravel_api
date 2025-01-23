<?php

use App\Http\Controllers\PlansController;
use App\Http\Controllers\ProductsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::post('/login', [AuthController::class, 'login']);

Route::group(['namespace' => 'App\Http\Controllers'], function () {
    Route::group(['prefix' => 'auth'], function () {
        Route::post('login', 'AuthController@login');
        Route::post('oauth-login', 'AuthController@OauthRegistration');
        Route::post('register', 'AuthController@register');
        Route::post('forgot-password', 'AuthController@forgotPassword')->middleware('guest')->name('password.email');
        Route::post('reset-password', 'AuthController@resetPassword')->middleware('guest')->name('password.reset');
    });
    // public route for viewing products
    Route::group(['prefix' => 'products'], function (): void {
        Route::get('/', [ProductsController::class, 'index']);
        Route::get('/view/{id}', [ProductsController::class, 'show']);
    });

    Route::group(['prefix' => 'categories'], function () {
        Route::get('/', 'CategoriesController@index');
    });

    Route::post('auth/resend-verify-email', 'AuthController@resendVerifyEmail')->middleware(['throttle:6,1']);

    // For Authenticated
    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::group(['prefix' => 'auth'], function () {
            Route::post('verify-email', 'AuthController@verifyEmail');
            Route::post('logout', 'AuthController@logout');
            Route::post('verify-token', 'AuthController@verifyToken');
            Route::post('change-password', 'AuthController@changePassword');
        });
        Route::group(['prefix' => 'plans'], function () {
            Route::get('/', [PlansController::class, 'index']);
            Route::get('/view/{id}', [PlansController::class, 'show']);
            Route::post('create', [PlansController::class, 'store']);
            Route::post('/update/{id}', [PlansController::class, 'update']);
            Route::delete('/delete/{id}', [PlansController::class, 'destroy']);
        });

        Route::group(['prefix' => 'products'], function (): void {
            Route::post('create', [ProductsController::class, 'store']);
            Route::post('/update/{id}', [ProductsController::class, 'update']);
            Route::delete('/delete/{id}', [ProductsController::class, 'destroy']);
        });

        Route::post('create-payment-intent', 'StripeController@paymentIntent');
        Route::post('create-checkout-session', 'StripeController@checkoutSession');
    });
});
