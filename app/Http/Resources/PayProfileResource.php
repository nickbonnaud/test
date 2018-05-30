<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class PayProfileResource extends Resource
{

  public function toArray($request)
  {
    return [
      'id' => $this->id,
      'slug' => $this->slug,
      'business_name' => $this->business_name,
      'logo' =>  $this->logo->apiUrl,
      'token' => $this->token
    ];
  }
}
