<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PublicController extends Controller
{
	public function showPrivacy() {
		return view('policies.privacy');
	}

	public function showEndUser() {
		return view('policies.end_user');
	}

	public function qbDisconnect() {
    return view('policies.qbDisconnect');
  }
}
