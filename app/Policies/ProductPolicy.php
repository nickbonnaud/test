<?php

namespace App\Policies;

use App\User;
use App\Product;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductPolicy
{
    use HandlesAuthorization;

  /**
   * Determine whether the user can view the product.
   *
   * @param  \App\User  $user
   * @param  \App\Product  $product
   * @return mixed
   */
  public function view(User $user, Product $product) {
    return $user->id == $product->profile->user->id;
  }

  /**
   * Determine whether the user can update the product.
   *
   * @param  \App\User  $user
   * @param  \App\Product  $product
   * @return mixed
   */
  public function update(User $user, Product $product) {
    return $user->id == $product->profile->user->id;
  }

  /**
   * Determine whether the user can delete the product.
   *
   * @param  \App\User  $user
   * @param  \App\Product  $product
   * @return mixed
   */
  public function delete(User $user, Product $product) {
    return $user->id == $product->profile->user->id;
  }
}
