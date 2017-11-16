<?php

namespace App\Http\Controllers\Api\Web;

use App\Product;
use App\Profile;
use App\Filters\ProductFilters;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductsController extends Controller {

	public function __construct() {
		$this->middleware('auth');
	}

	public function index(Profile $profile, ProductFilters $filters) {
    $this->authorize('view', $profile);
    $products = Product::filter($filters, $profile, $type = "all")->get();
    return ProductResource::collection($products);
  }

  public function syncSquare(Profile $profile) {
  	$this->authorize('view', $profile);
  	if (! $profile->account) return response()->json(['result' => 'no_account']);
  	if (!$profile->squareConnected()) return response()->json(['result' => 'not_connected']);
  	if (! $profile->account->squareLocationSet()) return response()->json(['result' => 'location_not_set']);

  	$result = Product::syncSquareInventory($profile);
  	return response()->json(['result' => $result]);
  }
}
