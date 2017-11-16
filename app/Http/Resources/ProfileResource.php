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
      'logo' =>  $this->logo->url,
      'hero' => $this->hero->url,
      'tags' => $this->tags,
      'website' => $this->website,
      'formatted_description' => $this->formatted_description,
    ];
  }
}
