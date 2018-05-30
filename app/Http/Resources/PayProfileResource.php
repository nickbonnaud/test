<?php

namespace App\Http\Resources;

use Carbon\Carbon;
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
      'token' => [
      	'value' => $this->token,
      	'expiry' => $this->token ? Carbon::now()->addMinutes(env('JWT_TTL'))->timestamp : null
      ]
    ];
  }
}
