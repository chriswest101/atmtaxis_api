<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('login', 'AuthController@login');
    Route::post('signup', 'AuthController@signup');

    Route::group([
      'middleware' => 'auth:api'
    ], function() {
        Route::get('logout', 'AuthController@logout');
        Route::get('user', 'AuthController@user');
    });
});

Route::group([
    'prefix' => 'bookings'
], function () {
    Route::post('', 'BookingsController@create');

    Route::group([
      'middleware' => 'auth:api'
    ], function() {
        Route::get('', ['uses' => 'BookingsController@getAll']);
        Route::get('{bookingId}', ['uses' => 'BookingsController@get', 'middleware' => 'AuthResource']);
    });
});

Route::group([
    'prefix' => 'quotes'
], function () {
    Route::post('', 'QuotesController@create');

    Route::group([
      'middleware' => 'auth:api'
    ], function() {
        Route::get('', ['uses' => 'QuotesController@getAll']);
        Route::get('{quoteId}', ['uses' => 'QuotesController@get', 'middleware' => 'AuthResource']);
    });
});

Route::group([
    'prefix' => 'prices'
], function () {
    Route::get('estimate', 'PricesController@getEstimate');
});


