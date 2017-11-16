<?php

namespace App\Http\Controllers\Api\Web;

use App\Post;
use App\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DealsController extends Controller {

	public function __construct() {
		$this->middleware('auth');
	}

	public function getPurchased(Post $post) {
		$this->authorize('view', $post);
		$purchased = Transaction::where('deal_id', '=', $post->id)->where('refunded', '=', false)->get();
    return response()->json($purchased);
	}
}
