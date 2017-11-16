<?php

namespace App\Policies;

use App\User;
use App\Account;
use Illuminate\Auth\Access\HandlesAuthorization;

class AccountPolicy
{
  use HandlesAuthorization;

  public function update(User $user, Account $account) {
    return $account->profile->user->id == $user->id;
  }

   public function view(User $user, Account $account) {
    return $account->profile->user->id == $user->id;
  }
}
