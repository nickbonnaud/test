<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();


// Business Review Routes
Route::post('profiles/{profile}/approve', 'BusinessReviewController@approve');
Route::post('profiles/{profile}/unapprove', 'BusinessReviewController@unapprove');

// Photos Routes
Route::post('photos/{profile}', 'PhotosController@storeWeb')->name('photos.storeWeb');
Route::delete('photos/{profile}', 'PhotosController@deleteWeb')->name('photos.deleteWeb');

// Tags Routes
Route::patch('tags/{profile}', 'TagsController@update')->name('tags.update');

// GeoLocation Routes
Route::patch('geoLocation/{geoLocation}', 'GeoLocationController@update')->name('geoLocation.update');

// Policy Routes
Route::get('policy/privacy', 'PublicController@showPrivacy')->name('policy.privacy');
Route::get('policy/end-user', 'PublicController@showEndUser')->name('policy.end-user');

// Accounts Routes
Route::get('accounts/{profile}/create', 'AccountsController@create')->name('accounts.create');
Route::post('accounts/{profile}', 'AccountsController@store')->name('accounts.store');
Route::get('accounts/{account}/edit', 'AccountsController@edit')->name('accounts.edit');
Route::patch('accounts/{account}', 'AccountsController@update')->name('accounts.update');
Route::get('accounts/{account}', 'AccountsController@show')->name('accounts.show');

// Posts routes
Route::get('posts/{profile}/{post}', 'PostsController@show')->name('posts.show');
Route::get('posts/{profile}', 'PostsController@index')->name('posts.profile');
Route::post('posts/{profile}', 'PostsController@store')->name('posts.store');
Route::delete('posts/{post}', 'PostsController@destroy')->name('posts.destroy');

// Events routes
Route::get('events/{profile}/{post}', 'EventsController@show')->name('events.show');
Route::get('events/{profile}', 'EventsController@index')->name('events.profile');
Route::post('events/{profile}', 'EventsController@store')->name('events.store');
Route::delete('events/{post}', 'EventsController@destroy')->name('events.destroy');

// Deals routes
Route::get('deals/{profile}/{post}', 'DealsController@show')->name('deals.show');
Route::get('deals/{profile}', 'DealsController@index')->name('deals.profile');
Route::post('deals/{profile}', 'DealsController@store')->name('deals.store');
Route::delete('deals/{post}', 'DealsController@destroy')->name('deals.destroy');

// Products routes
Route::get('products/{product}/edit', 'ProductsController@edit')->name('products.edit');
Route::get('products/{profile}', 'ProductsController@index')->name('products.profile');
Route::post('products/{profile}', 'ProductsController@store')->name('products.store');
Route::patch('products/{product}', 'ProductsController@update')->name('products.update');
Route::delete('products/{product}', 'ProductsController@destroy')->name('products.destroy');

// Loyalty Program routes
Route::get('loyalty-program/{profile}/create', 'LoyaltyProgramController@create')->name('loyaltyProgram.create');
Route::get('loyalty-program/{profile}', 'LoyaltyProgramController@show')->name('loyaltyProgram.show');
Route::post('loyalty-program/{profile}', 'LoyaltyProgramController@store')->name('loyaltyProgram.store');
Route::delete('loyalty-program/{profile}', 'LoyaltyProgramController@destroy')->name('loyaltyProgram.destroy');

// Sales History routes
Route::get('sales/{profile}', 'SalesHistoryController@show')->name('salesHistory.show');

// Employees routes
Route::get('team/{profile}', 'EmployeesController@show')->name('team.show');

// Post Analytics Dashboard routes
Route::get('analytics/posts/{profile}', 'AnalyticsDashboardController@show')->name('postAnalytics.show');

// Bill Routes
Route::get('bill/{profile}/{user}', 'BillController@show')->name('bill.show');


// User Routes
Route::get('users/{user}', 'UsersController@show')->name('users.show');
Route::patch('users/{user}', 'UsersController@update')->name('users.update');

// Connection Routes
Route::get('connections/{profile}', 'ConnectionsController@show')->name('connections.show');

// Quickbooks Routes
Route::get('quickbooks/redirect', 'QuickbooksController@redirectOauth');


// Web API routes
Route::prefix('api/web')->group(function () {

	// Deal Routes
	Route::get('deals/{post}', 'Api\Web\DealsController@getPurchased');

	// Profile Routes
	Route::patch('profiles/{profile}', 'Api\Web\ProfilesController@update')->name('webApiProfiles.update');

	// Sales History Routes
	Route::get('sales/{profile}', 'Api\Web\SalesHistoryController@getCustomDateRangeSales');

	// Employees Routes
	Route::patch('users/{profile}/{user}', 'Api\Web\EmployeesController@update');
	Route::get('users/{profile}/search', 'Api\Web\EmployeesController@index');

	// Transaction Routes
	Route::get('transactions/{profile}', 'Api\Web\TransactionsController@index');
	Route::post('transactions/{profile}/{user}', 'Api\Web\TransactionsController@Store');
	Route::patch('transactions/{profile}/{transaction}', 'Api\Web\TransactionsController@update');

	// PostAnalytics routes
	Route::get('analytics/posts/{profile}', 'Api\Web\PostAnalyticsController@index');

	// Post routes
	Route::get('posts/analytics/{profile}', 'Api\Web\PostsController@index');
	Route::get('posts/subscriptions/facebook', 'Api\Web\PostsController@verifyFacebook');
	Route::post('posts/subscriptions/instagram', 'Api\Web\PostsController@store');

	// UserLocation routes
	Route::get('location/customers/{profile}', 'Api\Web\UserLocationsController@index');

	Route::get('products/{profile}', 'Api\Web\ProductsController@index');
	Route::get('products/{profile}/sync', 'Api\Web\ProductsController@syncSquare');

	// Connection Routes
	Route::patch('connections/{profile}', 'Api\Web\ConnectionsController@update');
	Route::get('connections/facebook', 'Api\Web\ConnectionsController@connectFacebook');
	Route::get('connections/instagram', 'Api\Web\ConnectionsController@connectInstagram');
	Route::get('connections/square', 'Api\Web\ConnectionsController@connectSquare');
	Route::get('connections/qbo', 'Api\Web\ConnectionsController@connectQbo');
});

Route::resource('profiles', 'ProfilesController');
Route::resource('employees', 'EmployeesController');
Route::resource('loyalty', 'LoyaltyController');
Route::resource('refunds', 'RefundsController');

