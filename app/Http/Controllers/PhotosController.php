<?php

namespace App\Http\Controllers;

use App\Photo;
use App\Profile;
use Illuminate\Http\Request;
use App\Http\Requests\AddProfilePhotoRequest;
use App\Http\Requests\DeleteProfilePhotoRequest;

class PhotosController extends Controller
{
	public function __construct() {
    $this->middleware('auth');
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function storeWeb(Profile $profile, AddProfilePhotoRequest $request) {
    $this->authorize('update', $profile);
    if ($request->type == 'hero') {
      $photo = Photo::fromFormHero($request->file('photo'));
    } else {
      $photo = Photo::fromForm($request->file('photo'));
    }
    
    $photo->save();
    $profile->associatePhoto($request->type, $photo);
  }

  public function deleteWeb(Profile $profile, DeleteProfilePhotoRequest $request) {
    $this->authorize('delete', $profile);
    $type = $request->get('type');
    $photo = $profile->{$type};
    $profile->{$type}()->dissociate()->save();
    $photo->delete();
    return back();
  }
}
