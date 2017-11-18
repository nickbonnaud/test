<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ProfileTest extends TestCase
{
    use DatabaseMigrations;

    function test_an_authenticated_user_can_view_their_profile() {
        $this->signIn();
        $profile = create('App\Profile', ['user_id' => auth()->id()]);

        $this->get($profile->path())->assertSee('Customer Dashboard');
    }

    function test_an_unauthorized_user_cannot_view_a_profile() {
        $this->withExceptionHandling();
        $profile = create('App\Profile');
        $this->get($profile->path())->assertRedirect('/login');

        $this->signIn();
        $this->get($profile->path())->assertStatus(403);
    }

    function test_guests_may_not_create_a_profile() {
        $this->withExceptionHandling();
        
        $this->get('profiles/create')
            ->assertRedirect('/login');

        $this->post('/profiles')
            ->assertRedirect('/login');
    }    

    function test_authenticated_users_can_create_a_profile() {
        $this->signIn();
        $profile = make('App\Profile');
        $tax = create('App\Tax');
        $tags = create('App\Tag');
        $profile->county = "wake county";
        $profile->state = "nc";
        $profile->latitude = 34.78172000;
        $profile->longitude = -78.65666900;
        $profile->tags = [0 => $tags->id];
        $response = $this->post('/profiles', $profile->toArray());

        $response->assertRedirect($profile->path());

        $this->assertDatabaseHas('profiles', ['business_name' => $profile->business_name, 'tax_id' => $tax->id]);
        $this->assertDatabaseHas('geo_locations', ['identifier' => $profile->business_name]);
        $this->assertDatabaseHas('profile_tag', ['tag_id' => $tags->id] );

    }

    function test_an_unauthorized_user_cannot_view_edit_profile() {
        $this->withExceptionHandling();
        $profile = create('App\Profile');
        $this->get($profile->path() . '/edit')->assertRedirect('/login');

        $this->signIn();
        $this->get($profile->path() . '/edit')->assertStatus(403);
    }

    function test_an_authorized_user_can_view_edit_profile() {
        $this->signIn();
        $profile = create('App\Profile', ['user_id' => auth()->id()]);
        create('App\GeoLocation', ['profile_id' => $profile->id, 'identifier' => $profile->business_name]);
        $tag = create('App\Tag');
        $profile->tags()->sync($tag);

        $this->get($profile->path() . '/edit')->assertSee('Your Business Profile');
    }

    function test_an_unathorized_user_cannot_update_a_profile() {
        $this->withExceptionHandling();
        $profile = create('App\Profile');
        $name = str_random(10);
        $data = [
            'business_name' => $name,
            'website' => $profile->website,
            'description' => $profile->description,
            'slug' => $profile->slug,
            'user_id' => $profile->user_id
        ];
        $this->patch($profile->path(), $data)->assertRedirect('/login');

        $this->signIn();
        $this->patch($profile->path(), $data)->assertStatus(403);
    }

    function test_an_authorized_user_can_update_their_profile() {
        $this->signIn();
        $profile = create('App\Profile', ['user_id' => auth()->id()]);

        $name = str_random(10);
        $data = [
            'business_name' => $name,
            'website' => $profile->website,
            'description' => $profile->description,
            'slug' => $profile->slug,
            'user_id' => $profile->user_id
        ];

        $this->patch($profile->path(), $data);
        $this->assertDatabaseHas('profiles', ['business_name' => $name]);
    }

}
