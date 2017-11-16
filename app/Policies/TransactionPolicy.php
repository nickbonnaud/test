<?php

namespace App\Policies;

use App\User;
use App\Transaction;
use Illuminate\Auth\Access\HandlesAuthorization;

class TransactionPolicy
{
  use HandlesAuthorization;

  public function update(User $user, Transaction $transaction) {
    return $transaction->profile->user->id == $user->id;
  }
}
