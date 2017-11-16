<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductTest extends TestCase
{
	use RefreshDatabase;

	function test_an_unauthorized_user_cannot_view_products_index() {
		$this->withExceptionHandling();
		$profile = create('App\Profile');

		$this->get("products/{$profile->slug}")->assertRedirect('/login');
  	$this->signIn();
  	$this->get("products/{$profile->slug}")->assertStatus(403);
	}

	function test_an_an_authorized_user_can_view_products_index() {
		$this->signIn();
		$profile = create('App\Profile', ['user_id' => auth()->user()->id]);

  	$this->get("products/{$profile->slug}")->assertSee('Current Inventory');
	}

	function test_an_unauthorized_user_cannot_create_a_product() {
		$this->withExceptionHandling();
		$profile = create('App\Profile');
		$product = make('App\Product');
		

		$this->post("products/{$profile->slug}", $product->toArray())->assertRedirect('/login');
  	$this->signIn();
  	$this->post("products/{$profile->slug}", $product->toArray())->assertStatus(403);
	}

	function test_an_authorized_user_can_create_a_product() {
		$this->signIn();
		$profile = create('App\Profile', ['user_id' => auth()->id()]);
		$data = [
			'name' => 'Fake Product',
			'price' => '9.99',
			'description' => 'Some Description', 
			'category' => 'Category',
			'sku' => '123abc',
			'photo' => $file = UploadedFile::fake()->image('post.jpg')
		];

		Storage::fake('public');

		$response = $this->post("products/{$profile->slug}", $data);
		
		$this->assertDatabaseHas('products', ['profile_id' => $profile->id, 'name' => 'Fake Product']);
		$product = Product::first();
		$this->assertEquals('images/photos/' . $file->hashName(), $product->photo->path);
  	$this->get($response->headers->get('Location'))
      ->assertSee('Fake Product');
	}

	function test_an_unauthorized_user_cannot_view_product_edit() {
		$this->withExceptionHandling();
		$profile = create('App\Profile');
		$product = create('App\Product');
		

		$this->get("products/{$product->slug}/edit")->assertRedirect('/login');
  	$this->signIn();
  	$this->get("products/{$product->slug}/edit")->assertStatus(403);
	}

	function test_an_authorized_user_can_view_product_edit() {
		$this->signIn();
		$profile = create('App\Profile', ['user_id' => auth()->id()]);
		$product = create('App\Product', ['profile_id' => $profile->id, 'category' => 'Something Category']);
		create('App\Product', ['category' => 'Something else']);
		
		$this->get("products/{$product->slug}/edit")->assertSee($product->name, 'Something Category', 'Something else');
	}

	function test_an_unauthorized_user_cannot_update_a_product() {
		$this->withExceptionHandling();
		$profile = create('App\Profile');
		$product = create('App\Product');

		$data = [
			'name' => 'Fake New Product',
			'price' => '11.99',
		];

		$this->patch("products/{$product->slug}", $data)->assertRedirect('/login');
  	$this->signIn();
  	$this->patch("products/{$product->slug}", $data)->assertStatus(403);
	}

	function test_an_authorized_user_can_update_a_product() {
		$this->signIn();
		$profile = create('App\Profile', ['user_id' => auth()->id()]);
		$data = [
			'name' => 'Fake Product',
			'price' => '9.99',
			'description' => 'Some Description', 
			'category' => 'Category',
			'sku' => '123abc',
			'photo' => $file = UploadedFile::fake()->image('post.jpg')
		];

		Storage::fake('public');

		$this->post("products/{$profile->slug}", $data);

		$data = [
			'name' => 'Fake New Product',
			'price' => '11.99',
			'photo' => $newFile = UploadedFile::fake()->image('new.jpg')
		];

		$product = Product::first();
		$response = $this->patch("products/{$product->slug}", $data);

		$this->assertDatabaseHas('products', ['name' => 'Fake New Product', 'price' => 1199]);
		Storage::disk('public')->assertExists('images/photos/' . $newFile->hashName());
		
		$this->get($response->headers->get('Location'))
      ->assertSee('Fake New Product');
	}

	function test_an_unauthorized_user_cannot_destroy_a_product() {
		$this->withExceptionHandling();
		$profile = create('App\Profile');
		$product = create('App\Product');


		$this->delete("products/{$product->slug}")->assertRedirect('/login');
  	$this->signIn();
  	$this->delete("products/{$product->slug}")->assertStatus(403);
	}

	function test_an_authorized_user_can_destroy_a_product() {
		$this->signIn();
		$profile = create('App\Profile', ['user_id' => auth()->id()]);
		$data = [
			'name' => 'Fake Product',
			'price' => '9.99',
			'description' => 'Some Description', 
			'category' => 'Category',
			'sku' => '123abc',
			'photo' => $file = UploadedFile::fake()->image('post.jpg')
		];

		Storage::fake('public');
		$this->post("products/{$profile->slug}", $data);
		$product = Product::first();

		$response = $this->delete("products/{$product->slug}");
		
		$this->assertDatabaseMissing('products', ['id' => $product->id]);
		$this->get($response->headers->get('Location'))
      ->assertDontSee('Fake Product');
	}
}
