<?php

namespace App\Policies;

use App\User;
use App\Post;
use Illuminate\Auth\Access\HandlesAuthorization;

class PostPolicy
{
  use HandlesAuthorization;

  /**
   * Determine whether the user can delete the post.
   *
   * @param  \App\User  $user
   * @param  \App\Post  $post
   * @return mixed
   */
  public function delete(User $user, Post $post)
  {
    return $post->profile->user_id == $user->id;
  }

  public function view(User $user, Post $post) {
    return $post->profile->user_id == $user->id;
  }
}
