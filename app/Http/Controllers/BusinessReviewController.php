<?php

namespace App\Http\Controllers;

use App\Profile;
use Illuminate\Http\Request;

class BusinessReviewController extends Controller
{
	public function __construct() {
		$this->middleware('admin');
  }

  public function approve(Profile $profile, Request $request) {
  	if ($request->type == 'profile') {
  		$profile->approved = true;
  		$profile->save();
  	}
  	return redirect()->back();
  }

  public function unapprove(Profile $profile, Request $request) {
  	if ($request->type == 'profile') {
  		$profile->approved = false;
  		$profile->save();
  	}
  	return redirect()->back();
  }
}
