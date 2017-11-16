<?php

namespace App\Policies;

use App\User;
use App\GeoLocation;
use Illuminate\Auth\Access\HandlesAuthorization;

class GeoLocationPolicy
{
  use HandlesAuthorization;

  public function update(User $user, GeoLocation $geoLocation) {
    return $geoLocation->profile->user_id == $user->id;
  }
}
