<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
  use HandlesAuthorization;

  public function view(User $user, User $authUser) {
    return $user->id == $authUser->id;
  }

  public function update(User $user, User $authUser) {
    return $user->id == $authUser->id;
  }
}
