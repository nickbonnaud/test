<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class PhotoTest extends TestCase
{
  use DatabaseMigrations;

  function test_an_unauthorized_user_cannot_add_a_logo_photo() {
  	$this->withExceptionHandling();
  	$profile = create('App\Profile');

  	$data = [
  		'type' => 'logo',
  		'photo' => $file = UploadedFile::fake()->image('avatar.jpg')
  	];
  	$this->post("photos/{$profile->slug}", $data)->assertRedirect('/login');
  	$this->signIn();
    $this->post("photos/{$profile->slug}", $data)->assertStatus(403);
  }

  function test_an_unauthorized_user_cannot_add_a_hero_photo() {
  	$this->withExceptionHandling();
  	$profile = create('App\Profile');

  	$data = [
  		'type' => 'hero',
  		'photo' => $file = UploadedFile::fake()->image('avatar.jpg')
  	];
  	$this->post("photos/{$profile->slug}", $data)->assertRedirect('/login');
  	$this->signIn();
    $this->post("photos/{$profile->slug}", $data)->assertStatus(403);
  }

  function test_a_valid_photo_must_be_provided() {
  	$this->withExceptionHandling();
  	$this->signIn();
  	$profile = create('App\Profile', ['user_id' => auth()->id()]);

  	$data = [
  		'type' => 'hero',
  		'photo' => 'not an image'
  	];
  	$this->post("photos/{$profile->slug}", $data)->assertStatus(302);
  }

  function test_an_authorized_user_can_add_a_logo_photo() {
  	$this->signIn();
    Storage::fake('public');

  	$profile = create('App\Profile', ['user_id' => auth()->id()]);

  	$data = [
  		'type' => 'logo',
  		'photo' => $file = UploadedFile::fake()->image('logo.jpg')
  	];

  	$this->json('POST', "photos/{$profile->slug}", $data);

    $this->assertEquals('images/photos/' . $file->hashName(), auth()->user()->profile->logo->path);
    $this->assertEquals('images/photos/tn-' . $file->hashName(), auth()->user()->profile->logo->thumbnail_path);
  	Storage::disk('public')->assertExists('images/photos/' . $file->hashName());
    Storage::disk('public')->assertExists('images/photos/tn-' . $file->hashName());

  }

  function test_an_authorized_user_can_add_a_hero_photo() {
    $this->signIn();
    Storage::fake('public');

    $profile = create('App\Profile', ['user_id' => auth()->id()]);

    $data = [
      'type' => 'hero',
      'photo' => $file = UploadedFile::fake()->image('hero.jpg')
    ];

    $this->json('POST', "photos/{$profile->slug}", $data);

    $this->assertEquals('images/photos/' . $file->hashName(), auth()->user()->profile->hero->path);
    $this->assertEquals('images/photos/tn-' . $file->hashName(), auth()->user()->profile->hero->thumbnail_path);
    Storage::disk('public')->assertExists('images/photos/' . $file->hashName());
    Storage::disk('public')->assertExists('images/photos/tn-' . $file->hashName());
  }

  function test_an_unauthorized_user_cannot_delete_a_logo_or_hero() {
    $this->withExceptionHandling();
    $profile = create('App\Profile');

    $this->delete("photos/{$profile->slug}", ['type' => 'logo'])->assertRedirect('/login');
    $this->delete("photos/{$profile->slug}", ['type' => 'hero'])->assertRedirect('/login');
    $this->signIn();
    $this->delete("photos/{$profile->slug}", ['type' => 'logo'])->assertStatus(403);
    $this->delete("photos/{$profile->slug}", ['type' => 'hero'])->assertStatus(403);
  }

  function test_an_authorized_user_can_delete_a_logo_or_hero() {
    $this->signIn();
    Storage::fake('public');

    $profile = create('App\Profile', ['user_id' => auth()->id()]);

    $data = [
      'type' => 'logo',
      'photo' => $file = UploadedFile::fake()->image('hero.jpg')
    ];

    $this->json('POST', "photos/{$profile->slug}", $data);

    $this->delete("photos/{$profile->slug}", ['type' => 'logo']);

   
    Storage::disk('public')->assertMissing('public/images/photos/' . $file->hashName());
    
  }
}
