<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class GeoLocationResource extends Resource
{
  public function toArray($request)
  {
    return [
      'location_id' => $this->profile_id,
      'logo' => $this->profile->logo->thumbnail_url,
      'business_name' => $this->profile->business_name,
    ];
  }
}
