<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\User::class, function (Faker\Generator $faker) {
	static $password;

  return [
    'first_name' => $faker->firstName,
    'last_name' => $faker->lastName,
    'email' => $faker->unique()->safeEmail,
    'password' => $password ?: $password = bcrypt('secret'),
    'remember_token' => str_random(1),
  ];
});


$factory->define(App\Profile::class, function ($faker) {
  $name = $faker->company;
  $slug = str_slug($name, '-');

  return [
    'business_name' => $name,
    'website' => $faker->domainName,
    'description' => $faker->paragraph,
    'slug' => $slug,
    'user_id' => function() {
    	return factory('App\User')->create()->id;
    }
  ];
});

$factory->define(App\Tax::class, function () {
  return [
    'county' => 'wake',
		'state' => 'nc',
		'county_tax' => 100,
		'state_tax' => 100,
    'total' => 200
  ];
});

$factory->define(App\Tag::class, function ($faker) {
  return [
    'name' => $faker->word
  ];
});

$factory->define(App\GeoLocation::class, function ($faker) {
  $profile = factory('App\Profile')->create();
  return [
    'profile_id' => $profile->id,
    'identifier' => $profile->business_name,
    'latitude' => 34.78172123,
    'longitude' => -78.65666912
  ];
});

$factory->define(App\Account::class, function ($faker) {
  $profile = factory('App\Profile')->create();
  return [
    'profile_id' => $profile->id,
    'accountUserFirst' => $faker->firstName,
    'accountUserLast' => $faker->lastName,
    'ownership' => 75,
    'ownerEmail' => $faker->safeEmail,
    'accountEmail' => $faker->safeEmail,
    'dateOfBirth' => $faker->date('Y-m-d'),
    'ssn' => $faker->ssn,
    'indivStreetAddress' => $faker->streetAddress,
    'indivCity' => $faker->city,
    'indivState' => $faker->state,
    'indivZip' => $faker->postcode,
    'legalBizName' => $profile->business_name,
    'businessType' => 0,
    'established' => $faker->date('Y-m-d'),
    'annualCCSales' => 10000,
    'bizTaxId' => 12-3456789,
    'bizStreetAddress' => $faker->streetAddress,
    'bizCity' => $faker->city,
    'bizState' => $faker->state,
    'bizZip' => $faker->postcode,
    'phone' => $faker->phoneNumber,
    'accountNumber' => 987654321,
    'routing' => 123456789,
    'method' => 1,
    'status' => 'review'
  ];
});

$factory->define(App\Post::class, function ($faker) {
  $profile = factory('App\Profile')->create();
  return [
    'profile_id' => $profile->id,
    'message' => $faker->paragraph(2),
  ];
});

$factory->define(App\Product::class, function ($faker) {
  $profile = factory('App\Profile')->create();
  return [
    'profile_id' => $profile->id,
    'name' => $faker->word,
    'price' => '$ 9.99',
  ];
});

$factory->define(App\LoyaltyProgram::class, function ($faker) {
  $profile = factory('App\Profile')->create();
  return [
    'profile_id' => $profile->id,
    'reward' => $faker->name,
  ];
});

$factory->define(App\LoyaltyCard::class, function ($faker) {
  $user = factory('App\User')->create();
  return [
    'user_id' => $user->id,
  ];
});

$factory->define(App\Transaction::class, function ($faker) {
  $profile = factory('App\Profile')->create();
  $user = factory('App\User')->create();
  return [
    'profile_id' => $profile->id,
    'user_id' => $user->id,
    'paid' => true,
    'tax' => 100,
    'tips' => 200,
    'net_sales' => 500,
    'total' => 800,
  ];
});

$factory->define(App\PostAnalytics::class, function ($faker) {
  $user = factory('App\User')->create();
  $profile = factory('App\Profile')->create();
  $post = factory('App\Post')->create();
  return [
    'profile_id' => $profile->id,
    'user_id' => $user->id,
    'post_id' => $post->id,
    'viewed' => false
  ];
});

$factory->define(App\UserLocation::class, function ($faker) {
  $user = factory('App\User')->create();
  $profile = factory('App\Profile')->create();
  return [
    'profile_id' => $profile->id,
    'user_id' => $user->id,
  ];
});

$factory->define(App\PushToken::class, function ($faker) {
  $user = factory('App\User')->create();
  return [
    'user_id' => $user->id,
    'device' => 'Android',
    'push_token' => 'dYt4KnJ8_Uw:APA91bGeE_Vg3FTbEw4W0pgZTiZ25IP4R5q5izMWtWdBmjuLeuO2P0RITmGTNhmGbC3QL1Xf56MrrtXNnYS99YWTq11ytkAnaD0mMC4qrq4XJLDRp0CtpxJjAS9jMImQ52GAJ28s2Qb-'
  ];
});

$factory->define(App\City::class, function ($faker) {
  return [
    'name' => "Raleigh",
    'county' => 'Wake County',
    'state' => 'NC'
  ];
});

$factory->define(App\Photo::class, function ($faker) {
  return [
    'name'=> $faker->name,
    'path' => "fake/path.jpg",
    'thumbnail_path' => "fake/thumb/path.jpg"
  ];
});


