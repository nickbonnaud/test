<?php

namespace App\Http\Controllers;

use App\Product;
use App\Profile;
use App\Photo;
use App\Filters\ProductFilters;
use App\Http\Requests\CreateProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
  
  public function __construct() {
    $this->middleware('auth');
  }

  public function index(Profile $profile, ProductFilters $filters) {
    $this->authorize('view', $profile);
    $products = Product::filter($filters, $profile, $type = "all")->get();
    return view('products.product_index', compact('products', 'profile'));
  }


  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Profile $profile, CreateProductRequest $request) {
    $this->authorize('update', $profile);
    $product = (new Product($request->all()))
      ->addPhoto($file = $request->file('photo'));
    $profile->products()->save($product);
    return redirect()->route('products.profile', ['profiles' => $profile->slug]);
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  \App\Product  $product
   * @return \Illuminate\Http\Response
   */
  public function edit(Product $product, ProductFilters $filters) {
    $this->authorize('view', $product);
    $categories = Product::filter($filters, $profile = $product->profile, $type = "categories")->get();
    return view('products.edit', compact('product', 'categories'));
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Product  $product
   * @return \Illuminate\Http\Response
   */
  public function update(Product $product, UpdateProductRequest $request) {
    $this->authorize('update', $product);
    $product->updatePhoto($file = $request->file('photo'));
    $product->update($request->all());
    return redirect()->route('products.profile', ['profiles' => $product->profile->slug]);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Product  $product
   * @return \Illuminate\Http\Response
   */
  public function destroy(Product $product) {
    $this->authorize('delete', $product);
    if ($product->photo) {
      $product->destroyPhoto();
    }
    $product->delete();
    return redirect()->back();
  }
}
