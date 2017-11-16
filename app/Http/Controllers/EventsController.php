<?php

namespace App\Http\Controllers;

use App\Post;
use App\Profile;
use App\Photo;
use App\Filters\PostFilters;
use Illuminate\Http\Request;
use App\Http\Requests\CreateEventRequest;

class EventsController extends Controller
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
    $events = Post::filter($filters, $profile, $type = "profileEvents")->get();
    return view('events.profile_index', compact('events', 'profile'));
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Profile $profile, CreateEventRequest $request) {
    $this->authorize('update', $profile);
    $event = new Post($request->all());
    if ($file = $request->file('photo')) {
      $event->associatePhoto(Photo::fromForm($file));
    } 
    $profile->posts()->save($event);
    return redirect()->route('events.profile', ['profiles' => $profile->slug]);
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\Post  $post
   * @return \Illuminate\Http\Response
   */
  public function show(Profile $profile, Post $post) {
    $event = $post;
    return view('events.show', compact('profile', 'event'));
  }


  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Post  $post
   * @return \Illuminate\Http\Response
   */
  public function destroy(Post $post) {
    $this->authorize('delete', $post);
    $event = $post;
    $event->deletePost();
    return redirect()->back();
  }
}
