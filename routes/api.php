<?php

use Illuminate\Http\Request;

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

//Authentication routes
Route::prefix('auth')->group(function () {
	Route::get('me', 'AuthenticateController@me');
	Route::post('register', 'AuthenticateController@register');
	Route::post('login', 'AuthenticateController@login');
});

// Transaction routes
Route::get('transactions', 'TransactionsController@index');
Route::patch('transactions/{profile}', 'TransactionsController@update');
Route::post('transactions/{profile}', 'TransactionsController@store');

// Post routes
Route::prefix('v1')->group(function() {
	Route::get('posts', 'PostsController@index');
});

// Profile routes
Route::prefix('v1')->group(function() {
	Route::get('profiles', 'ProfilesController@index');
});

// City routes
Route::get('cities', 'CitiesController@index');

// Notification Routes
route::get('notifications', 'NotificationsController@index');

// User routes
Route::patch('user', 'UsersController@update');

// Loyalty Card routes
Route::get('loyalty', 'LoyaltyCardsController@index');

Route::post('location', 'GeoLocationsController@index');
