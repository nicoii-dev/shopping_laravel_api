<?php

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

    Route::post('auth/resend-verify-email', 'AuthController@resendVerifyEmail')->middleware(['throttle:6,1']);
    //for authenticated
    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::group(['prefix' => 'auth'], function () {
            Route::post('verify-email', 'AuthController@verifyEmail');
            Route::post('logout', 'AuthController@logout');
            Route::post('verify-token', 'AuthController@verifyToken');
            Route::post('change-password', 'AuthController@changePassword');
        });

        Route::post('create-payment-intent', 'StripeController@paymentIntent');
        Route::post('create-checkout-session', 'StripeController@checkoutSession');
    });
    Route::group(['prefix' => 'products'], function () {
        Route::get('/', 'ProductsController@index');
    });

});


