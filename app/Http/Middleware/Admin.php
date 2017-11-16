<?php

namespace App\Http\Middleware;

use Closure;

class Admin
{
  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure  $next
   * @return mixed
   */
  public function handle($request, Closure $next)
  {
    if (!auth()->check()) {
      if ($request->ajax()) {
        return response('Unauthorized.', 401);
      } else {
        return redirect('/login');
      }
    } else {
      if (!auth()->user()->is_admin) {
        return response('Forbidden.', 403);
      }
    }
    return $next($request);
  }
}
