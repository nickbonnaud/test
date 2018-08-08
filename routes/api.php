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
Route::get('user', 'UsersController@index');
Route::patch('user', 'UsersController@update');

// Loyalty Card routes
Route::get('loyalty', 'LoyaltyCardsController@index');
Route::patch('loyalty/{loyaltyCard}', 'LoyaltyCardsController@update');

// Georoutes
Route::post('location', 'GeoLocationsController@index');

// Geofence routes
Route::get('geofences', 'GeoFenceController@index');
Route::post('geofences', 'GeoFenceController@update');

// Card Vault
Route::get('card/vault', 'CardVaultController@show');
Route::post('card/vault/{user}', 'CardVaultController@store');

// Deals routes
Route::patch('deals/{transaction}', 'DealsController@update');

//Tags routes
Route::get('tags', 'TagsController@index');

// Push Token routes
Route::post('push-token', 'PushTokenController@store');

// Pusher Auth
Route::post('pusher/{user}', 'PusherController@authenticate');

// Post Analytics routes
Route::post('analytics/posts', 'AnalyticsController@store');


// Pay Clover Routes
Route::prefix('pay')->group(function() {
	Route::get('customers', 'PayCustomersController@index');
	Route::get('me', 'PayAuthenticateController@me');
	Route::get('transaction', 'PayTransactionsController@index');
	Route::get('employees', 'PayEmployeesController@index');
	Route::post('login', 'PayAuthenticateController@login');
	Route::patch('deal', 'PayDealsController@update');
	Route::patch('loyalty', 'PayLoyaltyCardsController@update');
	Route::post('transaction', 'PayTransactionsController@store');
	Route::patch('business', 'PayBusinessController@update');

	Route::post('subscription', 'PayWebHooksController@clover');
	Route::post('pusher/{profile}', 'PusherController@authenticateBusiness');
});

