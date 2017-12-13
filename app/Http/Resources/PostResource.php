<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class PostResource extends Resource
{
  public function toArray($request)
  {
    $profile = $this->profile;

    return [
      'id' => $this->id,
      'profile_id' => $profile->id,
      'profile_slug' => $profile->slug,
      'business_name' => $profile->business_name,
      'message' => $this->message,
      'title' => $this->title,
      'body' => $this->body,
      'photo_url' => isset($this->photo->url) ? $this->photo->apiUrl : $this->social_photo_url,
      'photo_thumb_url' => isset($this->photo->url) ? $this->photo->apiThumbnailUrl : null,
      'published_at' => $this->published_at,
      'event_date' => $this->event_date,
      'is_redeemable' => $this->is_redeemable,
      'deal_item' => $this->deal_item,
      'price' => $this->price,
      'end_date' => $this->end_date,
      'logo' =>  $this->profile->logo->apiUrl,
      'website' => $this->profile->website,
      'formatted_description' => $this->profile->formatted_description,
      'hero' => $this->profile->hero->apiUrl,
      'google_rating' => $this->profile->google_rating,
      'google_id' => $this->profile->google_id,
      'tags' => $this->profile->tags
    ];
  }
}
