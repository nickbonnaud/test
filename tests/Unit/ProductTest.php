<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductTest extends TestCase
{
	use RefreshDatabase;

	function test_a_post_belongs_to_a_profile() {
  	$product = create('App\Product');
  	$this->assertInstanceOf('App\Profile', $product->profile);
  }

  function test_a_product_sets_price_to_correct_format() {
  	$product = create('App\Product', ['price' => '$ 7.99']);
  	$this->assertDatabaseHas('products', ['price' => 799]);
  }

  function test_a_product_gets_price_to_correct_format() {
   	$product = create('App\Product', ['price' => '$ 7.99']);
    $this->assertEquals($product->price, 7.99);
  }

  function test_a_product_can_create_a_slug() {
    $product = create('App\Product');
    $this->assertEquals($product->slug, str_slug($product->name, '-'));
  }

  function test_a_product_slug_is_unique() {
    $productFirst = create('App\Product');
    $productSecond = make('App\Product', ['name' => $productFirst->name]);

    $this->assertNotEquals($productFirst->slug, $productSecond->slug);
  }
}
