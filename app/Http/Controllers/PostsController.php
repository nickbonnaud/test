<?php

namespace App\Http\Controllers;

use App\Post;
use App\Profile;
use App\Photo;
use App\Filters\PostFilters;
use App\Http\Requests\CreatePostRequest;
use Illuminate\Http\Request;

class PostsController extends Controller
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
    $posts = Post::filter($filters, $profile, $type = "profilePosts")->get();
    return view('posts.profile_index', compact('posts', 'profile'));
  }


  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Profile $profile, CreatePostRequest $request) {
    $this->authorize('update', $profile);
    $post = new Post($request->all());

    if ($file = $request->file('photo')) {
      $post->associatePhoto(Photo::fromForm($file));
    } 
    $profile->posts()->save($post);
    return redirect()->route('posts.profile', ['profiles' => $profile->slug]);
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\Post  $post
   * @return \Illuminate\Http\Response
   */
  public function show(Profile $profile, Post $post) {
    return view('posts.show', compact('profile', 'post'));
  }


  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Post  $post
   * @return \Illuminate\Http\Response
   */
  public function destroy(Post $post) {
    $this->authorize('delete', $post);
    $post->deletePost();
    return redirect()->back();
  }

  // protected function getPosts(Profile $profile) {
  //   $posts = Post::where('profile_id', '=', $profile->id)->whereNull('event_date')->orderBy('published_at', 'desc')->limit(10)->get();
  //   return $posts;
  // }
}
