<?php

namespace App;

use Carbon\Carbon;
use DateTimeZone;
use App\Photo;
use App\Profile;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\Log;

class Post extends Model {

	protected $fillable = [ 'message', 'event_date', 'title', 'body', 'deal_item', 'end_date', 'price', 'insta_post_id', 'social_photo_url', 'fb_post_id'];

  protected $dates = ['published_at'];
	protected $appends = ['formatted_body', 'formatted_message'];

	public static function boot() {
    parent::boot();

    static::saving(function(Post $post) {
      $post->published_at = Carbon::now(new DateTimeZone(config('app.timezone')));
      if ($post->price) {
        $post->is_redeemable = true;
      }
    });
  }

  public function profile() {
    return $this->belongsTo('App\Profile');
 	}

  public function photo() {
    return $this->belongsTo('App\Photo');
  }

 	public function setMessageAttribute($message) {
    $this->attributes['message'] = clean_newlines($message);
  }

  public function getFormattedMessageAttribute() {
    return html_newlines_to_p($this->message);
  }

  public function setBodyAttribute($body) {
    $this->attributes['body'] = clean_newlines($body);
  }

  public function getFormattedBodyAttribute() {
    return html_newlines_to_p($this->body);
  }

  public function setPriceAttribute($price) {
    $this->attributes['price'] =  preg_replace("/[^0-9\.]/","", $price) * 100;
  }

  public function getPriceAttribute($price) {
    return round($price / 100, 2);
  }

  public function associatePhoto($photo) {
    return $this->photo()->associate($photo);
  }

  public function deletePost() {
    if ($photo = $this->photo) {
      $this->photo()->dissociate()->save();
      $photo->delete();
    }
    $this->delete();
  }

  public function scopeFilter($query, $filters, $profile, $type = null) {
    return $filters->apply($query, $type)->where('profile_id', '=', $profile->id);
  }

  public function scopeApiFilter($query, $filters) {
    return $filters->apply($query)
      ->where('is_redeemable', '=', false)
      ->orWhere('end_date', '>', Carbon::now());
  }

  public static function processSubscription($postData, $isFacebook) {
    if ($isFacebook) {
      self::getProfileOfPost($postData);
    } else {
      self::processInstagramPost($postData);
    }
  }

  public static function getProfileOfPost($postData) {
    if ($postData['object'] == 'page') {
      foreach ($postData['entry'] as $entry) {
        if ($profile = Profile::where('fb_page_id', '=', $entry['id'])->first()) {
          self::processPostFacebook($entry, $profile);
        }
      }
    }
  }

  public static function processPostFacebook($entry, $profile) {
    Log::info($entry);
    foreach ($entry['changes'] as $postItem) {
      if ($postItem['field'] == 'feed') {
        $post = $postItem['value'];

        if ($post['item'] == 'status' || $post['item'] == 'photo' || $post['item'] == 'post' || $post['item'] == 'event') {
          switch ($post['verb']) {
            case 'add':
              self::addFbPost($post, $profile);
              break;
            case 'edited':
              self::editFbPost($post, $profile);
              break;
            case 'remove':
              self::deleteFbPost($post, $profile);
              break;
            default:
              return;
          }
        }
      }
    }
  }

  public static function addFbPost($fbPost, $profile) {
    if (isset($fbPost['event_id'])) {
      self::getEventData($fbPost['event_id'], $profile);
    } else {
       if (! Post::where('fb_post_id', '=', $fbPost['post_id'])->first()) {
        $post = new Post([
          'fb_post_id' => $fbPost['post_id'],
          'message' => isset($fbPost['message']) ? $fbPost['message'] : "Recently added photo",
        ]);
        if (isset($fbPost['photos'])) {
          $post->social_photo_url = $fbPost['photos'][0];
        } elseif (isset($fbPost['link'])) {
          $post->social_photo_url = $fbPost['link'];
        }
        $profile->posts()->save($post);
      }
    }
  }

  public static function getEventData($eventId, $profile) {
    if (! Post::where('fb_post_id', '=', $eventId)->first()) {
      $client = new Client(['base_uri' => 'https://graph.facebook.com/v2.8/']);
      try {
        $response = $client->request('GET', $eventId, [
          'query' => ['access_token' => $profile->fb_app_id]
        ]);
      } catch (GuzzleException $e) {
        if ($e->hasResponse()) {
          dd($e->getResponse());
        }
      }
      $event = json_decode($response->getBody());
      self::createEvent($event, $profile);
    }
  }

  public static function createEvent($event, $profile) {
    $url = self::getEventPhoto($event->id, $profile);
    $post = new Post([
      'fb_post_id' => $event->id,
      'title' => $event->name,
      'body' => $event->description,
      'event_date' => date('Y-m-d', strtotime($event->start_time)),
      'social_photo_url' => $url
    ]);
    $profile->posts()->save($post);
  }

  public static function getEventPhoto($eventId, $profile) {
    $client = new Client(['base_uri' => 'https://graph.facebook.com/v2.11/']);
      try {
        $response = $client->request('GET', $eventId . '/picture', [
          'query' => ['redirect' => '0', 'type' => 'large', 'access_token' => $profile->fb_app_id]
        ]);
      } catch (GuzzleException $e) {
        if ($e->hasResponse()) {
          dd($e->getResponse());
        }
      }
      $photo = json_decode($response->getBody());
      return $photo->data->url;
  }

  public static function editFbPost($fbPost, $profile) {
    if ($post = Post::where('fb_post_id', '=', $fbPost['post_id'])->first()) {
      $post->message = $fbPost['message'];
      if (isset($fbPost['photos'])) {
        $post->social_photo_url = $fbPost['photos'][0];
      } elseif (isset($fbPost['link'])) {
        $post->social_photo_url = $fbPost['link'];
      }
      $profile->posts()->save($post);
    }
  }

  public static function deleteFbPost($fbPost, $profile) {
    if ($post = Post::where('fb_post_id', '=', $fbPost['post_id'])->first()) {
      $post->delete();
    }
  }

  public static function processInstagramPost($postData) {
    foreach ($postData as $post) {
      $accountId = $post->object_id;
      $mediaId = $post->data->media_id;

      if ($profile = Profile::where('insta_account_id', '=', $accountId)->first()) {
        self::getInstagramPost($mediaId, $profile);
      }
    }
  }

  public static function getInstagramPost($mediaId, $profile) {
    $client = new Client(['base_uri' => 'https://api.instagram.com/v1/']);
    try {
      $response = $client->request('GET', 'media/' . $mediaId, [
        'query' => ['access_token' => $profile->insta_account_token]
      ]);
    } catch (GuzzleException $e) {
      if ($e->hasResponse()) {
        dd($e->getResponse());
      }
    }
    $instaPost = json_decode($response->getBody());
    self::addInstagramPost($instaPost, $profile, $mediaId);
  }

  public static function addInstagramPost($instaPost, $profile, $mediaId) {
    if ($instaPost->data->type == 'image') {
      $post = new Post([
        'insta_post_id' => $mediaId,
        'message' => isset($instaPost->data->caption->text) ? $instaPost->data->caption->text : "Recently added photo",
        'social_photo_url' => $instaPost->data->images->standard_resolution->url
      ]);

      $profile->posts()->save($post);
    }
  }
}
