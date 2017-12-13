<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class ProfileResource extends Resource
{
  public function toArray($request)
  {
    return [
      'id' => $this->id,
      'slug' => $this->slug,
      'business_name' => $this->name,
      'logo' =>  $this->logo->apiUrl,
      'hero' => $this->hero->apiThumbnailUrl,
      'tags' => $this->tags,
      'website' => $this->website,
      'description' => $this->description,
      'google_rating' => $this->google_rating,
      'google_id' => $this->google_id,
    ];
  }
}
