<?php

namespace App\Http\Controllers;

use App\Post;
use App\Profile;
use App\Photo;
use Illuminate\Http\Request;
use App\Filters\PostFilters;
use App\Http\Requests\CreateDealRequest;

class DealsController extends Controller
{
  public function __construct() {
    $this->middleware('auth', ['except' => 'show']);
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Profile $profile, PostFilters $filters) {
    $this->authorize('view', $profile);
    $deals = Post::filter($filters, $profile, $type = "profileDeals")->get();
    return view('deals.profile_index', compact('deals', 'profile'));
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Profile $profile, CreateDealRequest $request) {
    $this->authorize('update', $profile);
    $deal = new Post($request->all());
    if ($file = $request->file('photo')) {
      $deal->associatePhoto(Photo::fromForm($file));
    } 
    $profile->posts()->save($deal);
    return redirect()->route('deals.profile', ['profiles' => $profile->slug]);
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\Post  $post
   * @return \Illuminate\Http\Response
   */
  public function show(Profile $profile, Post $post) {
    $deal = $post;
    return view('deals.show', compact('profile', 'deal'));
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Post  $post
   * @return \Illuminate\Http\Response
   */
  public function destroy(Post $post) {
    $this->authorize('delete', $post);
    $deal = $post;
    $deal->deletePost();
    return redirect()->back();
  }
}
