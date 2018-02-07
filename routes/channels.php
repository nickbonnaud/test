<?php

use App\Profile;
use App\User;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('geofence.{profile}', function ($user, Profile $profile) {
  return $user->profile->slug === $profile->slug;
});

Broadcast::channel('reward.{profile}', function ($user, Profile $profile) {
  return $user->profile->slug === $profile->slug;
});

Broadcast::channel('bill-request.{profile}', function ($user, Profile $profile) {
  return $user->profile->slug === $profile->slug;
});

Broadcast::channel('transaction-error.{profile}', function ($user, Profile $profile) {
  return $user->profile->slug === $profile->slug;
});

Broadcast::channel('transactions-change.{profile}', function ($user, Profile $profile) {
  return $user->profile->slug === $profile->slug;
});

Broadcast::channel('transaction-success.{profile}', function ($user, Profile $profile) {
  return $user->profile->slug === $profile->slug;
});

Broadcast::channel('bill-push-success.{profile}', function ($user, Profile $profile) {
  return $user->profile->slug === $profile->slug;
});

Broadcast::channel('redeemed-item.{profile}', function ($user, Profile $profile) {
  return $user->profile->slug === $profile->slug;
});