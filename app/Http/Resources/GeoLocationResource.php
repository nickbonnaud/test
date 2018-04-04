<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class GeoLocationResource extends Resource
{
  public function toArray($request)
  {
    return [
      'location_id' => $this->profile_id,
      'latitude' => $this->latitude,
      'longitude' => $this->longitude,
      'logo' => $this->profile->logo->apiThumbnailUrl,
      'business_name' => $this->profile->business_name,
      'beacon' => [
        'uuid' => $this->profile->beacon->uuid,
        'identifier' => $this->profile_id
      ]
    ];
  }
}
