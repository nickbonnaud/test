<?php

namespace App\Http\Controllers;

use App\Profile;
use Illuminate\Http\Request;

class ConnectionsController extends Controller
{
	public function __construct() {
		$this->middleware('auth');
	}

	public function show(Profile $profile) {
		$this->authorize('view', $profile);
		return view('connections.show');
	}
}
