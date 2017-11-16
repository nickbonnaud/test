<?php

namespace App\Rules;

use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Validation\Rule;

class PasswordCheck implements Rule
{
  /**
   * Create a new rule instance.
   *
   * @return void
   */
  public function __construct($user)
  {
    $this->user = $user;
  }

  /**
   * Determine if the validation rule passes.
   *
   * @param  string  $attribute
   * @param  mixed  $value
   * @return bool
   */
  public function passes($attribute, $value)
  {
    return Hash::check($value, $this->user->password);
  }

  /**
   * Get the validation error message.
   *
   * @return string
   */
  public function message()
  {
      return 'Your current password is incorrect';
  }
}
